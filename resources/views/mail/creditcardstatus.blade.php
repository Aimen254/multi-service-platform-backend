@component('mail::message')
@switch($eventType)
    @case('card_expired')
        <div style="padding:4%; border-radius:4px">
            <h1 style="text-align: center;">Card Expired</h1>
            <div style="text-align: center; font-weight:600; text-transform:capitalize">{{$customer->first_name. ' ' . $customer->last_name}} Your card Having last four digits {{$card}}  has expired and deleted.</div>
        </div>
    @break
    @case('card_expiring_this_month')
        <div style="padding:4%; border-radius:4px">
            <h1 style="text-align: center;">Card Expiring This Month</h1>
            <div style="text-align: center; font-weight:600; text-transform:capitalize">{{$customer->first_name. ' ' . $customer->last_name}} Your card Having last four digits {{$card}}  is going to expire this month.</div>
        </div>
    @break
    @case('card_expiring_next_month')
        <div style="padding:4%; border-radius:4px">
            <h1 style="text-align: center;">Card Expiring Next Month.</h1>
            <div style="text-align: center; font-weight:600; text-transform:capitalize">{{$customer->first_name. ' ' . $customer->last_name}} Your card Having last four digits {{$card}}  is going to expire next month.</div>
        </div>
    @break
    @case('card_updated')
        <div style="padding:4%; border-radius:4px">
            <h1 style="text-align: center;">Card Updated</h1>
            <div style="text-align: center; font-weight:600; text-transform:capitalize">{{$customer->first_name. ' ' . $customer->last_name}} Your card Having last four digits {{$card}}  is updated.</div>
        </div>
    @break
    
    @default
        Default case...
@endswitch
@endcomponent