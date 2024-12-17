@component('mail::message')
<div style="padding:4%; border-radius:4px">
<h1 style="text-align: center;">Order Placed Order ID {{$order->order_id}}</h1>
<div style="text-align: center; font-weight:600">({{$user->first_name. ' ' . $user->last_name}}) Placed an order.</div>
</div>
<div style="padding: 3%; margin:2%; border: 1px solid #eee">
    <h1 style="margin-bottom:2%">Order Details</h1>
    <h3 style="border-bottom: 1px solid #eee; padding-bottom:1%">Product</h3>
    @foreach ($order->items as $item)
    <div style="display: flex; justify-content:space-between">
        <div>{{$item->product->name}} x {{$item->quantity}}</div>
        <div>${{numberFormat($item->total)}}</div>
    </div>
    @endforeach
    <div style="display: flex; justify-content:space-between; margin-top:2%; border-bottom:1px solid #eee; padding-bottom:1%">
        <div>
            <h3>Subtotal</h3>
        </div>
        <div>${{numberFormat($order->actual_price)}}</div>
    </div>
    @if($order->delivery_fee)
    <div style="display: flex; justify-content:space-between; margin-top:2%; border-bottom:1px solid #eee; padding-bottom:1%">
        <div>
            <h3>Delivery Fee</h3>
        </div>
        <div>${{numberFormat($order->delivery_fee)}}</div>
    </div>
    @endif
    @if($order->platform_commission)
    <div style="display: flex; justify-content:space-between; margin-top:2%; border-bottom:1px solid #eee; padding-bottom:1%">
        <div>
            <h3>Platform Fee</h3>
        </div>
        <div>${{numberFormat($order->platform_commission)}}</div>
    </div>
    @endif
    <div style="display: flex; justify-content:space-between; margin-top:2%; border-bottom:1px solid #eee; padding-bottom:1%">
        <div>
            <h3>Discount Price</h3>
        </div>
        <div>${{$order->discount_price}}</div>
    </div>
    @if ($order->tax_type == 1)
    <div style="display: flex; justify-content:space-between; margin-top:2%; border-bottom:1px solid #eee; padding-bottom:1%">
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
</div>
@endcomponent