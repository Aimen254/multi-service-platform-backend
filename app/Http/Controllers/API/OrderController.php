<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use Location\Polygon;
use App\Models\Address;
use App\Models\Setting;
use App\Models\Business;;

use Location\Coordinate;
use Stripe\StripeClient;
use App\Models\OrderItem;
use App\Models\CreditCard;
use App\Models\OrderStatus;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;
use App\Traits\StripePayment;
use App\Enums\PlatfromFeeType;
use App\Models\BusinessSetting;
use App\Events\OrderPlacedEmail;
use App\Events\OrderStatusEmail;
use App\Enums\ProductStockStatus;
use App\Traits\PushNotifications;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Transformers\OrderTransformer;
use App\Http\Requests\API\OrderRequest;
use App\Enums\Business\Settings\TaxType;
use TeamPickr\DistanceMatrix\DistanceMatrix;
use App\Enums\Business\Settings\DeliveryType;
use App\Enums\OrderStatus as OrderStatusEnum;
use App\Http\Requests\API\RecentOrdersRequset;
use App\Enums\Business\Settings\CustomDeliveryFee;
use TeamPickr\DistanceMatrix\Licenses\StandardLicense;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PhpParser\Node\Expr\Cast\Double;

class OrderController extends Controller
{
    use StripePayment, PushNotifications;
    protected $total_tax = null;
    protected StripeClient $stripeClient;
    public $status = null;
    public $message = null;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function index(RecentOrdersRequset $request)
    {
        try {
            $limit = $request->limit ? $request->limit : \config()->get('settings.pagination_limit');
            $user_id = \request()->user()->id;
            $statusList = [];
            $orderTotal = 0;
            if(request()->filled('business_uuid')) {
                $orderTotal = Order::whereHas('business', function($query) {
                    $query->where('uuid', request()->input('business_uuid'));
                })->sum('total');
            }
            $orders = Order::whereHas('items.product.business', function ($query) {
                $query->where('status', 'active')->when(request()->filled('business_uuid'), function($subquery) {
                    $subquery->where('uuid', request()->input('business_uuid'));
                });
            })->when(request()->filled('status'), function ($query) {
                $query->where('order_status_id', request()->input('status'));
            })
            ->with(['items.product' => function ($query) {
                $query->where('status', 'active');
            }, 'model', 'orderStatus'])
                ->where(function ($query) {
                    if (request()->from) {
                        $start_date = Carbon::parse(request()->from)->format('Y-m-d');
                        $query->whereDate('created_at', '>=', $start_date);
                    }
                    if(request()->to){
                        $end_date = Carbon::parse(request()->to)->format('Y-m-d');
                            $query->whereDate('created_at', '<=', $end_date);
                    }
                    if (request()->filled('keyword')) {
                        $query->whereRaw('order_id LIKE ?', ['%' . request()->input('keyword') . '%']);
                    }
                })->when(!request()->filled('business_uuid'), function($subquery) use($user_id) {
                    $subquery->where('model_id', $user_id);
                })->withCount('items')
                ->orderBy('id', 'desc')->paginate($limit);

            $paginate = apiPagination($orders, $limit);
            $options = [
                'withVariants' => request()->input('withVariants') ? request()->input('withVariants') : false,
                'ordersList' => request()->input('ordersList') ? request()->input('ordersList') : false,
                'withCustomer' => request()->filled('withCustomer') ? request()->input('withCustomer') : false
            ];
            $orders = (new OrderTransformer)->transformCollection($orders, $options);
                $statusList = OrderStatus::get();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $orders,
                'statusList' => $statusList,
                'totalAmount' =>  numberFormat($orderTotal),
                'meta' => $paginate,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function viewOrder($uuid)
    {
        try {
            $options = [
                'withCustomer' => true,
                'withDetail' => true
            ];
            $order = Order::whereUuid($uuid)->with(['items.product', 'model', 'items.productVariant.color', 'items.productVariant.size'])->firstOrFail();
            $order = (new OrderTransformer)->transform($order, $options);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $order,
                'statusList' => OrderStatus::get()
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function placeOrder(OrderRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = request()->user();
            $cart = $user->cart;
            $cartActualPrice = $cart->items->sum('actual_price');
            $business = Business::find($request->business_id);

            // If you need to store recepient_id in the cart table as well
            $cart->update([
                'recepient_id' => $request->input('recepient')
            ]);
            if ($cart && $cart->items()->where('business_id', $business->id)->count() > 0) {
                $minimumPurchase = $business->settings->where('key', 'minimum_purchase')
                    ->first()->value;
                    $cartActualPrice = floatval($cartActualPrice);
                    $minimumPurchase = floatval($minimumPurchase);
                if ($cartActualPrice < $minimumPurchase) {
                    return response()->json([
                        'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                        'message' => 'Your order is less than minimum purchase of this store'
                    ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }

                $orderStatus = OrderStatus::where('status', 'pending')->first();
                //getting tax value and conditional parameters.
                $businsessTax = $business->settings()->where('key', 'tax_percentage')
                    ->first();
                $taxApplicable = $business->settings()->where('key', 'tax_apply')->first();
                $taxModel = Setting::where('key', 'tax_model')->first();
                $taxModelValue = TaxType::coerce(str_replace(' ', '', ucwords($taxModel->value)))->value;

                // if ($request->input('orderType') == 'delivery' || $request->input('orderType') == 'mail') {
                //     $address = Address::find($request->input('shipping'));
                //     if ($request->input('orderType') == 'delivery') {
                //         if (!$this->checkDeliveryAvailable($business, $address)) {
                //             return response()->json([
                //                 'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                //                 'message' => 'You are out of delivery zone !'
                //             ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                //         }
                //     }
                // }

                $order = $user->orders()->create([
                    'business_id' => $request->business_id,
                    'order_status_id' => $orderStatus->id,
                    'order_type' => $request->orderType,
                    'discount_price' => $request->discount_price,
                    'discount_type' => $cart->coupon ? $cart->coupon->discount_type : NULL,
                    'discount_value' => $cart->coupon ? $cart->coupon->discount_value : '',
                    'delivery_fee' => $request->delivery_fee,
                    'mailing_id' => $request->input('mailing_id'),
                    'selected_card' => $request->input('selected_card'),
                    'billing_id' => $request->billing,
                    'shipping_id' =>$request->shipping,
                    'platform_fee_type' => $request->input('platform_fee_type'),
                    'platform_fee_value' => $request->input('platform_fee_value'),
                    'platform_commission' => $request->input('platform_fee'),
                    'coupon_id' => $cart->coupon ? $cart->coupon->id : null,
                    'payment_intent_id'=>$request->payment_intent_id,
                    'recepient_id' => $request->input('recepient') ?  $request->input('recepient') : null,
                ]);

                $items = $cart->items()->with(['productVariant.color', 'productVariant.size'])->where('business_id', $request->business_id)->get();
                foreach ($items as $key => $item) {
                    if ($request->input('orderType') == 'delivery') {
                        if ($item->product->is_deliverable != 1) {
                            DB::rollBack();
                            return response()->json([
                                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                                'message' => $item->product->name . " is not deliverable"
                            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                            break;
                        }
                    }

                    if ($item->product->stock_status == ProductStockStatus::outOfStock || ($item->product->stock > 0 && $item->quantity > $item->product->stock)) {
                        DB::rollBack();
                        return response()->json([
                            'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                            'message' => $item->product->name . " is out of stock"
                        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                        break;
                    }

                    if ($item->product->stock != -1 && $item->product->stock_status == ProductStockStatus::inStock) {
                        $item->product->decrement('stock', $item->quantity);
                        if ($item->product->stock == 0) {
                            $item->product->stock_status = ProductStockStatus::outOfStock;
                        }
                        $item->product->save();
                    }

                    $varinat = $item->productVariant;
                    $orderItem = $order->items()->create([
                        'product_id' => $item->product->id,
                        'quantity' => $item->quantity,
                        'actual_price' => $item->actual_price,
                        'unit_price' => $item->unit_price,
                        'discount_price' => $item->discount_price,
                        'tax_value' => $item->tax,
                        'total' => $item->total,
                        'unit_price' => $item->unit_price,
                        'color' => $varinat ? ($varinat?->color?->title ?? $varinat?->custom_color?->title ?? null) : null,
                        'size' => $varinat ? ($varinat?->size?->title ?? $varinat?->custom_size?->title ?? null) : null
                    ]);

                    if ($taxApplicable->value) {
                        if ($taxModelValue == TaxType::TaxIncludedOnPrice) {
                            $this->taxCalculation($orderItem, $businsessTax);
                            $order->update([
                                'tax_type' => TaxType::TaxIncludedOnPrice
                            ]);
                        } else {
                            $order->update([
                                'tax_type' => TaxType::TaxNotIncludedOnPrice
                            ]);
                        }
                    }
                }
                //getting updated order after adding order items
                $order = Order::find($order->id);
                if ($order) {
                    //charge customer for order , used Stripe Payment trait
                    $paymentCharged = $this->chargePayment($order);
                    $this->status = $paymentCharged['status'];
                    $this->message = $paymentCharged['message'];
                    // Stripe code ends
                    if ($cart->items()->where('business_id', '!=', $request->business_id)->count() > 0) {
                        $cart->items()->where('business_id', $request->business_id)->delete();
                        $cart->update(['coupon_id', NULL]);
                    } else {
                        $cart->delete();
                    }
                    event(new OrderPlacedEmail($order));

                    // sending push notifications to devices
                    $this->orderNotification($order);
                }
                $options = [
                    'withCustomer' => true,
                    'withDetail' => true
                ];
                $order = (new OrderTransformer)->transform($user->orders()->find($order->id), $options);
                DB::commit();
                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'data' => $order,
                    'stripe_status' => $this->status,
                    'stripe_message' => $this->message,
                ], JsonResponse::HTTP_OK);
            } else {
                DB::rollBack();
                return response()->json([
                    'status' => JsonResponse::HTTP_NOT_FOUND,
                    'message' => 'Cart is empty'
                ], JsonResponse::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function calculateDeliveryFee(Request $request)
    {
        $business = Business::whereUuid($request->business_uuid)->first();
        if ($request->type == 'recent') {
            $address = request()->user()->addresses()->find($request->address);
        }
        $license = new StandardLicense('AIzaSyB6_25wMJBgEeLqkRTBxC0aCXppPeXXCdQ');

        $deliveryType = DeliveryType::coerce(str_replace(' ', '', ucwords($business->deliveryZone->delivery_type)))->value;
        if ($deliveryType == DeliveryType::PlatformDelivery) {
            $user = User::whereUserType('newspaper')->first();
            $platformZone = $user->deliveryZone()->where('platform_delivery_type', $business->deliveryZone->platform_delivery_type)->first();
        }
        $response = DistanceMatrix::license($license)
            ->addOrigin(isset($platformZone) ? $platformZone->address : $business->address)
            ->addDestination($request->type == 'recent' ? $address->address : $request->address)
            ->setUnits('imperial')
            ->request();



        if ($response->json['rows'][0]['elements'][0]['status'] == 'OK') {
            $addressData = [
                'latitude' => $request->type == 'recent' ? $address->latitude : $request->latitude,
                'longitude' => $request->type == 'recent'
                    ? $address->longitude : $request->longitude,
            ];
            if (!$this->checkDeliveryAvailable($business, $addressData)) {
                return response()->json([
                    'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => 'You are out of delivery zone !'
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            // google map distance between store address and customer address
            $distanceInMiles = (float)$response->json['rows'][0]['elements'][0]['distance']['text'];
            $deliverySetting = isset($platformZone) ? $platformZone : $business->deliveryZone;
            $feetype = CustomDeliveryFee::coerce(str_replace(' ', '', ucwords($deliverySetting->fee_type)))->value;;

            if ($feetype === CustomDeliveryFee::DeliveryFeeByMileage) {
                if ($deliverySetting->mileage_distance >= $distanceInMiles) {
                    return \response()->json([
                        'status' => JsonResponse::HTTP_OK,
                        'data' => [
                            'delivery_fee' => numberFormat($distanceInMiles * $deliverySetting->mileage_fee),
                            'distance' => $response->json['rows'][0]['elements'][0]['distance']['text']
                        ]
                    ], JsonResponse::HTTP_OK);
                } else {
                    // if distance is greater than the first set milage
                    $extraMiles = $distanceInMiles - $deliverySetting->mileage_distance;
                    $extraMilesCost = $extraMiles * $deliverySetting->extra_mileage_fee;
                    $milesCost = $deliverySetting->mileage_distance * $deliverySetting->mileage_fee;
                    return \response()->json([
                        'status' => JsonResponse::HTTP_OK,
                        'data' => [
                            'delivery_fee' => numberFormat($extraMilesCost + $milesCost),
                            'distance' => $response->json['rows'][0]['elements'][0]['distance']['text']
                        ]
                    ], JsonResponse::HTTP_OK);
                }
            } else {
                $percentageOfTotalSale = $request->actual_price / $deliverySetting->percentage_amount;
                if ($percentageOfTotalSale > $deliverySetting->fixed_amount) {
                    return \response()->json([
                        'status' => JsonResponse::HTTP_OK,
                        'data' => [
                            'delivery_fee' => numberFormat($percentageOfTotalSale),
                        ]
                    ], JsonResponse::HTTP_OK);
                } else {
                    return \response()->json([
                        'status' => JsonResponse::HTTP_OK,
                        'data' => [
                            'delivery_fee' => numberFormat($deliverySetting->fixed_amount),
                        ]
                    ], JsonResponse::HTTP_OK);
                }
            }
        }
        return \response()->json([
            'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            'message' => 'Can not deliver to this address'
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function checkDeliveryAvailable($business, $address)
    {
        $deliveryZone = $business->deliveryZone;
        $deliveryType = DeliveryType::coerce(str_replace(' ', '', ucwords($business->deliveryZone->delivery_type)))->value;
        if ($deliveryType == DeliveryType::PlatformDelivery) {
            $newspaper = User::whereUserType('newspaper')->first();
            $deliveryZone = $newspaper->deliveryZone()->where('platform_delivery_type', $deliveryZone->platform_delivery_type)->first();
        }

        if ($deliveryZone->zone_type == 'polygon') {
            $points = json_decode($deliveryZone->polygon);
            $geofence = new Polygon();
            foreach ($points as $point) {
                $geofence->addPoint(new Coordinate($point->lat, $point->lng));
            }
            $insidePoint = new Coordinate($address['latitude'], $address['longitude']);

            return $geofence->contains($insidePoint);
        } else {
            $distance =  vincentyGreatCircleDistance($address['latitude'], $address['longitude'], $deliveryZone->latitude, $deliveryZone->longitude);
            return $distance <= $deliveryZone->radius;
        }
    }

    private function taxCalculation($item, $businsessTax)
    {
        if ($item->product->tax_percentage) {
            $this->taxCalculater($item, $item->product->tax_percentage);
        } else {
            $this->taxCalculater($item, $businsessTax->value);
        }
    }

    function getTopParent($category)
    {
        $parent = $category;
        if ($category->parent_id) {
            $parent = $category->parent;
            self::getTopParent($parent);
        }
        return $parent;
    }

    function taxCalculater($item, $tax_percentage)
    {
        $tax = ($item->total * $tax_percentage) / 100;
        $item->update([
            'tax_value' => $tax
        ]);
        $this->total_tax = $this->total_tax + $tax;
    }

    public function cancelOrder(Request $request)
    {
        $order = Order::whereUuid($request->input('order_uuid'))->first();
        $previousOrderStatusId = $order->order_status_id;
        //Gettings accepted order status from DB
        $orderStatusCancelled = OrderStatus::where('status', 'cancelled')->first();
        //Gettings processing order status from DB
        $orderStatusProcessing = OrderStatus::where('status', 'Processing')->first();
        //Business Owner
        $businessOwner = $order->business->businessOwner;
        if ($order->selected_card) {
            return $this->cancelPayment($order);
        } else {
            $order->update([
                'order_status_id' => $orderStatusCancelled->id,
            ]);
            //sending mail to customer.
            event(new OrderStatusEmail($order, (string)OrderStatusEnum::Cancelled, $businessOwner));
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Order Status Changed Successfully.',
            ], JsonResponse::HTTP_OK);
        }
    }

    //Item refund Code is not used, it is used to refund item from frontend which we are not using
    public function itemRefund(Request $request)
    {
        try {
            $orderItem = OrderItem::findOrFail($request->input('id'));
            $order = $orderItem->order;
            //get previous order status
            $previousStatusId = $order->order_status_id;
            //all refunded items
            $allRefundedOrderItems = $order->items->where('refunded', 1);
            //get previous order status
            $previousStatusId = $order->order_status_id;
            //Gettings processing order status from DB
            $orderStatusProcessing = OrderStatus::where('status', 'Processing')->first();
            //Getting refunded order status from DB
            $orderStatusRefunded = OrderStatus::where('status', 'refunded')->first();
            //business owner
            $businessOwner = $order->business->businessOwner->load('addresses', 'deliveryZone');
            //new order item total
            $totalRefund = $orderItem->total;
            if (count($order->items) - count($allRefundedOrderItems) == 1) {
                $allItemsRefunded = true;
                $applicationFeeRefund = true;
                $totalRefund += $order->platform_commission + $order->delivery_fee;
                $totalRefund = (float)number_format((float)$totalRefund, 2, '.', '');
            } else {
                $applicationFeeRefund = false;
                $allItemsRefunded = false;
                $totalRefund = (float)number_format((float)$totalRefund, 2, '.', '');
            }
            if ($order->selected_card) {
                //item refund for online payment
                $refundedItemPayment = $this->refundItemPayment($order, $orderItem, $totalRefund, $applicationFeeRefund, $allItemsRefunded, $previousStatusId, $orderStatusProcessing, $businessOwner);
                $status = $refundedItemPayment['status'];
                $message = $refundedItemPayment['message'];
            } else {
                //Item refund for cash on pickup
                if ($allItemsRefunded == 'true') {
                    $status = $orderStatusRefunded->id;
                    $orderStatusEnum = (string)OrderStatusEnum::Refunded;
                    $orderRefunded = 1;
                    $proportionalPlatformFee = $order->platform_commission;
                    $proportionalDeliveryFee = $order->delivery_fee;
                } else {
                    $status = $previousStatusId;
                    $orderStatusEnum = (string)OrderStatusEnum::Partially_Refunded;
                    $orderRefunded = 0;
                    $proportionalPlatformFee = 0;
                    $proportionalDeliveryFee = 0;
                }
                $refundedTotal = $order->amount_refunded + $orderItem->total;
                $orderItem->update([
                    'refunded' => 1,
                ]);
                $order->update([
                    'refunded' => $orderRefunded,
                    'amount_refunded' => $refundedTotal,
                    'order_status_id' => $status,
                    'refunded_delivery_fee' => $proportionalDeliveryFee,
                    'refunded_platform_fee' => $proportionalPlatformFee,
                ]);
                //sending mail to customer.
                event(new OrderStatusEmail($order, $orderStatusEnum, $businessOwner));
                $status = 'success';
                $message = 'Item Refunded Successfully.';
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $message,
                'isCompletRefunded' => $allItemsRefunded,
                'order' => $order->refresh(),
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateOrderStatus(Request $request, $businessUuid, $id) {

        //Getting Pending order status from DB
        $orderStatusPending = OrderStatus::where('status', 'pending')->first();
        //Getting refunded order status from DB
        $orderStatusRefunded = OrderStatus::where('status', 'refunded')->first();
        //Gettings accepted order status from DB
        $orderStatusAccepted = OrderStatus::where('status', 'accepted')->first();
        //Gettings cancelled order status from DB
        $orderStatusCancelled = OrderStatus::where('status', 'cancelled')->first();
        //Gettings rejected order status from DB
        $orderStatusRejected = OrderStatus::where('status', 'rejected')->first();
        //Gettings processing order status from DB
        $orderStatusProtry = OrderStatus::where('status', 'Processing')->first();
        //getting business owner
        $businessOwner = Business::where('uuid', $businessUuid)->firstOrFail()->businessOwner->load('addresses', 'deliveryZone');
        $message = null;
        $status = null;

        try {
            $order = Order::with('shippingAddress', 'orderTracking', 'coupon', 'orderStatus', 'items')->findOrFail($id);
            //get previous order status
            $previousStatusId = $order->order_status_id;
            //Emails will not be sent to these statuses from this code but will be sent from webhooks
            $statusIdsExcluded = [
                $orderStatusAccepted->id,
                $orderStatusRefunded->id,
                $orderStatusRejected->id
            ];
            $order->update($request->all());
            $status = 'success';
            $message = 'Order status updated successfully';

            if (!in_array($request->order_status_id, $statusIdsExcluded)) {
                //sending mail to customer.
                event(new OrderStatusEmail($order, $request->order_status_id, $businessOwner));

                // sending push notifications to devices
                $this->orderNotification($order);
            } elseif (!$order->selected_card) {
                //sending mail to customer.
                event(new OrderStatusEmail($order, $request->order_status_id, $businessOwner));

                // sending push notifications to devices
                $this->orderNotification($order);
            }

            //charge customer for order
            if ($order->charged && !$order->captured && $request->order_status_id == $orderStatusAccepted->id && $order->selected_card) {
                $capturedPayment = $this->capturePayment($order);
                $status = $capturedPayment['status'];
                $message = $capturedPayment['message'];
            }

            //Managing Customer's refund
            if ($request->order_status_id == $orderStatusRefunded->id) {
                if ($order->selected_card) {
                    if ($order->captured) {
                        if (!$order->refunded) {
                            $refundedPayment = $this->refundPayment($order);
                            $status = $refundedPayment['status'];
                            $message = $refundedPayment['message'];
                        } else {
                            $order->update([
                                'order_status_id' => $previousStatusId,
                            ]);
                            $status = 'danger';
                            $message = 'You have already refunded this order.';
                        }
                    } else {
                        $order->update([
                            'order_status_id' => $previousStatusId,
                        ]);
                        $status = 'danger';
                        $message = 'Can not make a refund before accepting the order.';
                    }
                } else {
                    $order->update([
                        'refunded' => 1,
                        'amount_refunded' => $order->total,
                        'order_status_id' => $orderStatusRefunded->id,
                    ]);
                }
            }

            //Managing Order Cancel
            if ($request->order_status_id == $orderStatusRejected->id) {
                if ($order->selected_card) {
                    if ($order->charged) {
                        if (!$order->captured) {
                            $rejectedPayment = $this->rejectPayment($order, $request);
                            $status = $rejectedPayment['status'];
                            $message = $rejectedPayment['message'];
                        } else {
                            $order->update([
                                'order_status_id' => $previousStatusId,
                            ]);
                            $status = 'danger';
                            $message = 'You payment has been captured. You can not cancel this order.';
                        }
                    } else {
                        $order->update([
                            'order_status_id' => $previousStatusId,
                        ]);
                        $status = 'danger';
                        $message = 'Card has not been charged.';
                    }
                } else {
                    $order->update([
                        'order_status_id' => $orderStatusRejected->id,
                        'rejection_reason' => $request->input('message'),
                    ]);
                    $status = 'success';
                    $message = 'Order Status Changed Successfully.';
                    //sending rejection email to user
                    event(new OrderStatusEmail($order, (string)OrderStatusEnum::Rejected, $businessOwner));
                }
            }
            return response()->json([
                'order' => $order->refresh(),
                'status' => JsonResponse::HTTP_OK,
                'message' => $message
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
