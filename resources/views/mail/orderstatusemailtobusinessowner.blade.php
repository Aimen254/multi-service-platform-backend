@component('mail::message')
<div style="padding:4%; border-radius:4px">
    <h1 style="text-align: center;">Order {{ $type == 12 ? 'Partially Refunded' : str_replace('_', ' ', ucwords($order->orderStatus->status))}}</h1>
    <div style="text-align: center; font-weight:600; text-transform:capitalize">Order status of {{$user->first_name. ' ' . $user->last_name}} having id {{ $order->order_id }} is changed to {{ $type == 12 ? 'Partially Refunded' : str_replace('_', ' ', ucwords($order->orderStatus->status))}}.</div>
</div>

<div style="padding: 3%; margin:2%; border: 1px solid #eee">
    <h1 style="margin-bottom:2%">Order Details</h1>
    <h3 style="border-bottom: 1px solid #eee; padding-bottom:1%">Product</h3>
    @foreach ($order->items as $item)
    <div style="display: flex; justify-content:space-between">
        <div>{{$item->product->name}} x {{$item->quantity}}</div>
        @if ($item->refunded )
            <div>(Refunded)</div>
        @endif
        <div>${{numberFormat($item->total)}}</div>
    </div>
    @endforeach
    <div
        style="display: flex; justify-content:space-between; margin-top:2%; border-bottom:1px solid #eee; padding-bottom:1%">
        <div>
            <h3>Subtotal</h3>
        </div>
        <div>${{numberFormat($order->actual_price)}}</div>
    </div>
    @if($order->delivery_fee)
    <div
        style="display: flex; justify-content:space-between; margin-top:2%; border-bottom:1px solid #eee; padding-bottom:1%">
        <div>
            <h3>Delivery Fee</h3>
        </div>
        <div>${{numberFormat($order->delivery_fee)}}</div>
    </div>
    @endif
    @if($order->platform_commission)
    <div
        style="display: flex; justify-content:space-between; margin-top:2%; border-bottom:1px solid #eee; padding-bottom:1%">
        <div>
            <h3>Platform Fee</h3>
        </div>
        <div>${{numberFormat($order->platform_commission)}}</div>
    </div>
    @endif
    <div
        style="display: flex; justify-content:space-between; margin-top:2%; border-bottom:1px solid #eee; padding-bottom:1%">
        <div>
            <h3>Discount Price</h3>
        </div>
        <div>${{numberFormat($order->discount_price)}}</div>
    </div>
    @if ($order->tax_type == 1)
        <div
            style="display: flex; justify-content:space-between; margin-top:2%; border-bottom:1px solid #eee; padding-bottom:1%">
            <div>
                <h3>Tax</h3>
            </div>
            <div>${{numberFormat($order->total_tax_price)}}</div>
        </div>
    @endif
    <div style="display: flex; justify-content:space-between; margin-top:2%; border-bottom:2px solid #eee; padding-bottom:1%">
        <div>
            <h3>Grand Total</h3>
        </div>
        <div>${{numberFormat($order->total)}}</div>
    </div>
    @if ($eventType == '2')
    <div style="display: flex; justify-content:space-between; margin-top:2%;">
        <div>
            <h3>Total Amount Charged</h3>
        </div>
        <div>${{$order->total}}</div>
    </div>
    @elseif ($eventType == '10' || $eventType == '12')
    <div style="display: flex; justify-content:space-between; margin-top:2%;">
        <div>
            <h3>Total Amount Refunded</h3>
        </div>
        <div>${{numberFormat($order->amount_refunded) }}</div>
    </div>
    @else
        
    @endif

@endcomponent