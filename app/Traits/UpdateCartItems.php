<?php
namespace App\Traits;
use Carbon\Carbon;
use App\Models\Setting;
use App\Models\CartItem;
use App\Enums\Business\Settings\TaxType;

trait UpdateCartItems
{
    /**
     * To apply discount on all variants of a product
     *
     * @param $product
     */

    public static function updateCart($cartItems) {
        $date = Carbon::now()->format('Y-m-d');
        if($cartItems && $cartItems->count() > 0) {
            foreach ($cartItems as $key => $item) {
                $product = $item->productVariant ? $item->productVariant : $item->product;
                $data = $product->product ? [
                    'unit_price' => $product->discount_price && $product->product->discount_start_date <= $date && $product->product->discount_end_date >= $date ? $product->discount_price : $product->price,
                    'actual_price' => $product->discount_price && $product->product->discount_start_date <= $date && $product->product->discount_end_date >= $date ? $product->discount_price * $item->quantity : $product->price * $item->quantity,
                    'total' => $product->discount_price && $product->product->discount_end_date >= $date && $product->product->discount_start_date <= $date
                    ? $product->discount_price * $item->quantity : $product->price * $item->quantity,
                ] : [
                    'unit_price' => $product->discount_price && $product->discount_start_date <= $date && $product->discount_end_date >= $date ? $product->discount_price : $product->price,
                    'actual_price' => $product->discount_price && $product->discount_start_date <= $date && $product->discount_end_date >= $date ? $product->discount_price * $item->quantity : $product->price * $item->quantity,
                    'total' => $product->discount_price && $product->discount_end_date >= $date && $product->discount_start_date <= $date
                    ? $product->discount_price * $item->quantity : $product->price * $item->quantity,
                ];
                $item->update($data);
                self::taxCalculation($item);
            }

        }
    }


    // tax calculation
    public static function taxCalculation($item)
    {
        $product = $item->product;
        $businsessTax = $product->business->settings()->where('key', 'tax_percentage')->first();
        $taxApplicable = $product->business->settings()->where('key', 'tax_apply')->first();
        $taxModel = Setting::where('key', 'tax_model')->first();
        $taxModelValue = TaxType::coerce(str_replace(' ', '', ucwords($taxModel->value)))->value;
        if ($taxApplicable->value) {
            if ($taxModelValue == TaxType::TaxNotIncludedOnPrice) {
                if($product->tax_percentage) {
                    self::taxCalculater($item, $product->tax_percentage);
                } 
                else {
                    self::taxCalculater($item, $businsessTax->value);
                }
            } 
        } 
    } 

    public static function taxCalculater($item, $tax_percentage)
    {
        $tax = ($item->total * $tax_percentage) / 100;
        $total = $item->total + $tax;
        $item->update([
            'tax' => $tax,
            'total' => $total
        ]);
    }

    
}