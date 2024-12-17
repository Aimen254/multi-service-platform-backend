@extends('mail.layouts.mail')
@section('content')
<div style="padding:20px 0px 15px 0px; border-radius:4px; border-bottom: 1px solid #eee;">
    @if ($mail_to_business_owner)
    <div class="fs-14 text-center" style="font-weight:600"><strong>Order status having ID {{$order->order_id}} is changed to {{orderStatus($order->orderStatus?->status)}}.</strong></div>
    @else
    <div class="fs-14" style="text-align: center;">Hello <strong>{{$user->first_name}}</strong></div>
    <div class="fs-14" style="text-align: center; font-weight:600"><strong>Your Order {{$order->order_id}} is {{orderStatus($order->orderStatus?->status)}}.</strong></div>
    @endif
</div>
<div class="text-center py-15">
    <button class="py-10 px-40 text-light bg-secondary" style="border: 0px; border-radius: 3px;">Track Order</button>
</div>
<table>
    <tbody class="py-10">
        <tr>
            <td colspan="2" class="text-center fs-16-bold">Order ID {{$order->order_id}}</td>
        </tr>
        <tr>
            <td class="text-left bg-secondary fs-16-bold">Business Details</td>
            <td class="text-right bg-secondary fs-16-bold">Customer Details</td>
        </tr>
        <tr>
            <td class="fs-td">
                <span>{{$order->business?->name}}</span><br>
                @if($business_owner->addresses->count() > 0)
                <span>{{$business_owner->addresses[0]->address}}</span><br>
                @endif
                <span>-</span><br>
                <span>{{$business_owner->email}}</span>
            </td>
            <td class="text-right fs-td">
                <span>{{$user->first_name}} {{$user->last_name}}</span><br>
                <span>{{$order->shippingAddress?->street_address}}, {{$order->shippingAddress?->address}}</span><br>
                <span>{{$user->phone}}</span><br>
                <span>{{$user->email}}</span>
            </td>
        </tr>
        <tr>
            @if($order->order_type !== 'pick_up')
            <td class="fs-td" style="border-top: 1px solid #eee" >
                <span class="fs-14-bold">Customer Name: </span><br>
                <span class="fs-14">{{$user->first_name}} {{$user->last_name}}</span><br><br>
                <span class="fs-14-bold">Delivery Location: </span><br>
                <span class="fs-14">{{$order->shippingAddress?->street_address}}, {{$order->shippingAddress?->address}}</span><br><br>
            </td>
            @endif
        
        </tr>
        <tr>
            <td colspan="2" class="text-center bg-secondary fs-16-bold">Order Details</td>
        </tr>
        <tr>
            <td class="fs-td">
                <span class="fs-14">Order ID {{$order->order_id}}</span><br><br>
                @foreach ($order->items as $item)
                <span class="fs-14-bold py-10">{{$item->quantity}} x {{$item->product->name}}</span><br>
                @endforeach
            </td>
            <td class="text-right fs-td">
                <br>
                @foreach ($order->items as $item)
                <span class="fs-14-bold py-10">${{numberFormat($item->total)}}</span><br>
                @endforeach
            </td>
        </tr>
        <tr>
            <td class="text-left bg-secondary">
                <span class="fs-14">Subtotal </span><br>
                @if ($order->tax_type == 1)
                <span class="fs-14">Tax</span><br>
                @endif
                <span class="fs-14">Delivery Fee</span><br>
                <span class="fs-14">Platform Fee</span><br>
                <span class="fs-14">Discount</span><br>
                <span class="fs-16-bold">Total</span><br>
            </td>
            <td class="text-right bg-secondary">
                <span class="fs-14">${{numberFormat($order->actual_price)}}</span><br>
                @if ($order->tax_type == 1)
                <span class="fs-14">${{numberFormat($order->total_tax_price)}}</span><br>
                @endif
                <span class="fs-14">${{numberFormat($order->delivery_fee)}}</span><br>
                <span class="fs-14">${{numberFormat($order->platform_commission)}}</span><br>
                <span class="fs-14">${{numberFormat($order->discount_price)}}</span><br>
                <span class="fs-16-bold">${{numberFormat($order->total)}}</span><br>
            </td>
        </tr>
        <tr>
            <td class="text-left fs-14">
                Payment Method:
                @if ($order->selected_card)
                <span>stripe Connect</span>
                @else
                <span>Cash On Delivery</span>
                @endif
            </td>
            <td class="text-right fs-14"> Delivery Method: @if($order->order_type === 'pick_up')  {{ str_replace('_', ' ', $order->order_type) }}
                @else
                {{ $order->order_type }}
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="2" class="bg-dark text-dark">footer</td>
        </tr>
    </tbody>
</table>
@endsection