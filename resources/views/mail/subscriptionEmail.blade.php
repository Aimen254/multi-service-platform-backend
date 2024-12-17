@component('mail::message')
    <div style="padding:4%; border-radius:4px">
        <h1 style="text-align: center;">Subscription Plan {{ $invoice->product->name }}</h1>
        @if ($invoice->hookType == 'invoice_paid')
            <div style="text-align: center; font-weight:600; text-transform:capitalize">You payment for subscription plan
                {{ $invoice->product->name }} has been charged successfully.</div>
        @endif
        @if ($invoice->hookType == 'invoice_payment_failed')
            <div style="text-align: center; font-weight:600; text-transform:capitalize; padding-bottom: 10px">Yous payment
                for subscription plan {{ $invoice->product->name }} has been failed.</div>
            <div style="text-align: left; font-weight:600; text-transform:capitalize">Payment Fail Reason:
                {{ $invoice->paymentIntent->last_payment_error ? $invoice->paymentIntent->last_payment_error->message : '' }}
            </div>
        @endif
        @if ($invoice->hookType == 'subscription_cancelled')
            <div style="text-align: center; font-weight:600; text-transform:capitalize; padding-bottom: 10px">Your
                subscription plan {{ $invoice->product->name }} has been cancelled. All your data us saved, please subscribe again to activate your businesses. </div>
        @endif
        @if ($invoice->hookType == 'invoice_upcoming')
            <div style="text-align: center; font-weight:600; text-transform:capitalize; padding-bottom: 10px">Upcoming
                invoice for subscription plan {{ $invoice->product->name }}.</div>
        @endif
        @if ($invoice->hookType == 'customer_subscription_updated')
            <div style="text-align: center; font-weight:600; text-transform:capitalize; padding-bottom: 10px">Subscription
                plan updated.</div>
        @endif
        <div style="text-align: left; font-weight:600; text-transform:capitalize">Subscription Status:
            {{ $invoice->subscription->status }} </div>
    </div>
    @if ($invoice->hookType == 'invoice_paid' ||
        $invoice->hookType == 'invoice_payment_failed' ||
        $invoice->hookType == 'invoice_upcoming' ||
        $invoice->hookType == 'customer_subscription_updated')
        <div style="padding: 3%; margin:2%; border: 1px solid #eee">
            <h1 style="margin-bottom:2%">Plan Details</h1>
            <h3 style="border-bottom: 1px solid #eee; padding-bottom:1%">Product</h3>
            @foreach ($invoice->lines->data as $key => $line)
                <div style="display: flex; justify-content:space-between">
                    <div>{{ $line->description }}</div>
                    <div>${{ numberFormat($line->amount / 100) }}</div>
                </div>
            @endforeach
            <div
                style="display: flex; justify-content:space-between; margin-top:2%; border-bottom:1px solid #eee; padding-bottom:1%">
                <div>
                    <h3>Subtotal</h3>
                </div>
                <div>${{ numberFormat($invoice->subtotal / 100) }}</div>
            </div>
            <div
                style="display: flex; justify-content:space-between; margin-top:2%; border-bottom:2px solid #eee; padding-bottom:1%">
                <div>
                    <h3>Grand Total</h3>
                </div>
                <div>${{ numberFormat($invoice->total / 100) }}</div>
            </div>
        </div>
    @endif
@endcomponent
