<?php

namespace App\Transformers;

use App\Transformers\Transformer;
use Illuminate\Support\Facades\Log;
use App\Transformers\OrderItemsTransformer;

class OrderTransformer extends Transformer
{

    public function transform($order, $options = null)
    {
        $deliveryFlag = request()->input('delivery_type') && (request()->input('delivery_type')) == 'delivery' ?  true : false;
        $items = $order->items()->whereHas('product', function ($query) {
            $query->where('status', 'active');
        })->when($deliveryFlag, function ($query) {
            $query->whereHas('product', function ($subQuery) {
                $subQuery->where('is_deliverable', '!=', 0);
                $subQuery->whereHas('business.deliveryZone', function ($query) {
                    $query->where('delivery_type', '!=', 0);
                });
            });
        })->get();
        $data = [
            'id' => (int) $order->id,
            'uuid' => (string) $order->uuid,
            'order_id' => (string) $order->order_id,
            'order_status_id' => (string) $order->order_status_id,
            'order_type' => (string) $order->order_type,
            'business_name'=>$order->business->name,
            'total' => numberFormat($order->total),
            'actual_price' => numberFormat($order->actual_price),
            'status' => (string) $order->orderStatus->status,
            'order_items_count' => (int) $order->items()->count(),
            'tax' => $order->total_tax_price ? numberFormat($order->total_tax_price) : '',
            'tax_type' => $order->tax_type,
            'discount_price' => $order->discount_price ? numberFormat($order->discount_price) : '',
            'discount_value' => $order->discount_value
                ? numberFormat($order->discount_value) : '',
            'discount_type' => (string) $order->discount_type,
            'delivery_fee' => $order->delivery_fee ? numberFormat($order->delivery_fee) : '',
            'platform_fee_type' => $order->platform_fee_type,
            'platform_fee_value' => $order->platform_fee_value ? numberFormat($order->platform_fee_value) : '',
            'platform_commission' => $order->platform_commission ? numberFormat($order->platform_commission) : '',
            'refunded' => (bool) $order->refunded,
            'amount_refunded' => $order->amount_refunded ? numberFormat($order->amount_refunded) : '',
            'refunded_platform_fee' => $order->refunded_platform_fee ? numberFormat($order->refunded_platform_fee) : '',
            'refunded_delivery_fee' => $order->refunded_delivery_fee ? numberFormat($order->refunded_delivery_fee) : '',
            'total_amount_refunded' => numberFormat($order->refunded_delivery_fee + $order->refunded_platform_fee + $order->amount_refunded),
            'total_after_refund' => numberFormat($order->total - $order->amount_refunded - $order->refunded_platform_fee - $order->refunded_delivery_fee),
            'rejection_reason' => (string) $order->rejection_reason,
            'payment_intent_id' => $order->payment_intent_id,
            'created_at' => timeFormat($order->created_at),
        ];
        if (!(isset($options['ordersList']) && $options['ordersList'])) {
            $data['order_items'] = (new OrderItemsTransformer)->transformCollection($items, $options);
        }
        if (isset($options['withDetail']) && $options['withDetail']) {
            $data['note'] = (string) $order->note;
            $data['shipping_date'] = timeFormat($order->shippng_date);
            $data['selected_card'] = (string) $order->selected_card;
            $data['items'] = (int) $items->count();
            $data['shipping_address'] = $order->shippingAddress ? (new AddressTransformer)->transform($order->shippingAddress) : null;
            $data['billing_address'] = $order->billingAddress ? (new AddressTransformer)->transform($order->billingAddress) : null;
            $data['order_track'] = $order && $order->orderTracking->count() > 0 ? (new OrderTrackTransformer)->transformCollection($order->orderTracking) : [];
        }
        if (isset($options['withCustomer']) && $options['withCustomer']) {
            if ($order->recepient_id) {
                $data['customer'] = (new CustomerTransformer)->transform($order->recepient);
            } else {
                $data['customer'] = (new CustomerTransformer)->transform($order->model);
            }
        }
        return $data;
    }
}
