<?php

namespace App\Transformers;

use stdClass;
use Carbon\Carbon;
use App\Models\Business;;

use App\Models\CartItem;
use App\Enums\PlatfromFeeType;
use App\Traits\UpdateCartItems;
use App\Transformers\Transformer;
use App\Transformers\CouponTransformer;
use App\Transformers\MailingTransformer;
use App\Transformers\BusinessTransformer;
use App\Transformers\CartItemTransformer;
use App\Transformers\CustomerTransformer;
use App\Transformers\DeliveryZoneTransformer;
use App\Transformers\Business\SettingTransformer;

class CartTransformer extends Transformer
{
    protected $total = 0;
    protected $discount_price = 0;
    public function transform($cart, $options = null)
    {
                
        if (request()->has('business_id')) {
            $cartValues = $this->cartTotals($cart);
            $businesses = $cart->items()->where('business_id', request()->business_id)
                ->get()->groupBy('business_id');
            $busienssOptions = [
                'withDeliveryZone' => true,
                'withMailing' => isset($options['withMailing']) && $options['withMailing'],
                'mailingPrice' => isset($options['withMailing']) && $options['withMailing']
                    ? $cartValues['total'] : NULL,
                'withDetails' => true,
            ];
        } else {
            $businesses = $cart->items()->get()->groupBy('business_id');
        }
        $data = [
            'uuid' => $cart->uuid,
            'customer' => (new CustomerTransformer)->transform($cart->customer),
            'businesses' => $this->businessDetails($businesses, $cart, $busienssOptions ?? NULL),
            'coupon' => $cart->coupon ? (new CouponTransformer)->transform($cart->coupon) : null,

        ];
        return $data;
        UpdateCartItems::updateCart($cart->items);
        $data = [
            'uuid' => $cart->uuid,
            'actual_price' => numberFormat($cart->actual_price),
            'discount_price' => numberFormat($cart->discount_price),
            'total' => numberFormat($cart->total) +  numberFormat($this->calculatePlatformFee($cart,$cart->busines)),
            'discount_value' => $cart->coupon ? numberFormat($cart->coupon->discount_value) : '',
            'discount_type' => $cart->coupon ? $cart->coupon->discount_type : '',
            'cart_items' => (new CartItemTransformer)->transformCollection($cart->items),
            'customer' => (new CustomerTransformer)->transform($cart->customer),
            'business' => (new BusinessTransformer)->transform($cart->business, $busienssOptions),
            'coupon' => $cart->coupon ? (new CouponTransformer)->transform($cart->coupon) : null,
            'remove' => request()->remove_coupon ? true : false,
            'delivery_fee' => numberFormat(0),
            'tax' => numberFormat($cart->tax),
            'platform_fee' => numberFormat($this->calculatePlatformFee($cart,$cart->business)),
            'platform_fee_type' => $cart->business->settings()->where('key', 'custom_platform_fee_type')->first()->value,
            'platform_fee_value' => $cart->business->settings()->where('key', 'custom_platform_fee_value')->first()->value,
        ];
        return $data;
    }

    private function businessDetails($items, $cart, $options = NULL)
    {
        if ($cart->coupon_id) {
            $this->discountCalcultion($cart, $cart->coupon);
        }
        $data = [];

        if ($options && \request()->has('business_id')) {
            $business = Business::find(request()->business_id);
            if (isset($options['withMailing']) && $options['withMailing']) {
                $cartTotal = (int)round($options['mailingPrice']);
                $mails = $business->mails()->where('status', 'active')->where('minimum_amount', '<=', $cartTotal)->get();
                $mailing = (new MailingTransformer)->transformCollection($mails);
            }
            if (isset($options['withDetails']) && $options['withDetails']) {
                $deliveryZone = (new DeliveryZoneTransformer)->transform($business->deliveryZone);
                $settings = (new SettingTransformer)->transformCollection($business->settings);
            }
        }


        foreach ($items as $key => $item) {
            $actualPrice =numberFormat(array_sum(array_column($item->toArray(), 'actual_price')));
            $business = Business::find($key);
            if($business){
                $data[] = [
                    'id' => (int) $business->id,
                    'uuid' =>  $business->uuid,
                    'name' => (string) $business->name,
                    'thumbnail' =>  $business->thumbnail
                        ? getImage($business->thumbnail->path, 'image') : getImage(NULL, 'image'),
                   'address'=>$business->address,
                   'street_address'=>$business->street_address,
                   'city'=>$business->city,
                    'cart_items' => (new CartItemTransformer)->transformCollection($item),
                    'mailing' => isset($mailing) ? $mailing : [],
                    'deliveryZone' => isset($deliveryZone) ? $deliveryZone : new stdClass(),
                    'settings' => isset($settings) ? $settings : [],
                    'total' => $this->total > 0  ? numberFormat($this->total + $this->calculatePlatformFee($cart, $business))  : numberFormat($this->getUpdatedCartTotal($cart) + $this->calculatePlatformFee($cart, $business)),
                    'discount_price' => $this->discount_price > 0 ? numberFormat($this->discount_price) : numberFormat($this->getUpdateCartDiscount($cart)),
                    'actual_price' =>$actualPrice,
                    'tax' => numberFormat($this->totalTax($item)),
                    'delivery_fee' => numberFormat(0),
                    'platform_fee' => numberFormat($this->calculatePlatformFee($cart, $business)),
                    'platform_fee_type' => $business->settings()->where('key', 'custom_platform_fee_type')->first()->value,
                    'platform_fee_value' => numberFormat($business->settings()->where('key', 'custom_platform_fee_value')->first()->value),
                ];
            }
        }
        return $data;
    }

    private function discountCalcultion($cart, $coupon)
    {

        $cartTotal = $cart->items()->where('business_id', request()->business_id)->sum('actual_price');
        $cartTax = $cart->items()->where('business_id', request()->business_id)->sum('tax');
        if ($coupon->model_id == request()->business_id || request()->input('business')) {
            $date = Carbon::now()->format('Y-m-d');
            if ($coupon->end_date >= $date) {
                switch ($coupon->coupon_type) {
                    case 'business':
                        if (!request()->remove_coupon) {
                            
                            $discount = $coupon->discount_type == 'percentage'
                                ? ($cartTotal * $coupon->discount_value) / 100
                                : $coupon->discount_value;

                            $this->discount_price = $discount;
                            $this->total = calculateTotalPrice($cartTotal, $cartTax, $discount, true);
                        } else {
                            $this->discount_price = 0;
                            $this->total = calculateTotalPrice($cartTotal, $cartTax);
                            removeCoupon($cart);
                        }
                        break;
                    case 'product':
                        $this->productDiscount($cart, $coupon, $coupon->coupon_type, request()->remove_coupon ? \true : \false);
                        break;
                }
            } else {
                $this->discount_price = 0;
                $this->total = calculateTotalPrice($cartTotal, $cartTax);
                removeCoupon($cart);
            }
        } else {
            $this->total = calculateTotalPrice($cartTotal, $cartTax);
            removeCoupon($cart);
        }
    }

    private function productDiscount($cart, $coupon, $type, $removeCoupon = false)
    {
        $items = $cart->items()->where(function ($query) use ($type, $coupon) {
            $query->whereHas('product.coupons', function ($subQuery) use ($coupon) {
                $subQuery->where('coupon_id', $coupon->id);
            });
        })->get();
        if (count($items) > 0) {
            foreach ($items as $key => $item) {
                $discount = 0;
                if (!$removeCoupon) {
                    $discount = $coupon->discount_type == 'percentage'
                        ? ($item->actual_price * $coupon->discount_value) / 100
                        : ($item->actual_price >= $coupon->discount_value
                            ? $coupon->discount_value : $discount
                        );
                    $item->update([
                        'discount_price' => $discount,
                        'total' => calculateTotalPrice($item->actual_price, $item->tax, $discount, $removeCoupon ? false : true)
                    ]);
                } else {
                    $item->update([
                        'discount_price' => 0,
                        'total' => calculateTotalPrice($item->actual_price, $item->tax, $discount, $removeCoupon ? false : true)
                    ]);
                }
            }
        } else {
            removeCoupon($cart);
            return true;
        }

        if ($removeCoupon) {

           
            $cart->update(['coupon_id' => NULL]);
        }
        return true;
    }

    private function calculatePlatformFee($cart, $business)
    {
        //getting platform fee type from business settings
        $platformFeeType = $business->settings()->where('key', 'custom_platform_fee_type')->first();
        //getting platform fee value from business settings
        $platformFeeValue = $business->settings()->where('key', 'custom_platform_fee_value')->first();
        //changing platform fee type value
        $platformFeeTypeCanged = PlatfromFeeType::coerce(str_replace(' ', '', ucwords($platformFeeType->value)))->value;
        $cartTotal = $cart->items()->where('business_id', request()->business_id)->sum('actual_price');
        $newspaperFee = $platformFeeTypeCanged == 1 ? ($cartTotal) * ($platformFeeValue->value / 100) : $platformFeeValue->value;
        return $newspaperFee;
    }

    private function cartTotals($cart)
    {
        $cartItem = CartItem::where('business_id', \request()->business_id)->where('cart_id', $cart->id);
        return [
            'total' => $cartItem->sum('total')
        ];
    }

    private function getUpdatedCartTotal($cart)
    {
        $total = $cart->items()->where('business_id', request()->business_id)->sum('total');
        return $total;
    }

    private function getUpdateCartDiscount($cart)
    {
        $total = $cart->items()->where('business_id', request()->business_id)->sum('discount_price');
        return $total;
    }

    private function totalTax($item) {
        $taxes = array_column($item->toArray(), 'tax');

        // Remove commas and cast to float
        $taxes = array_map(function($tax) {
            return (float) str_replace(',', '', $tax);
        }, $taxes);

        // Sum the values
        $totalTax = array_sum($taxes);

        return $totalTax;

    }
}
