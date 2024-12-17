<?php

namespace App\Listeners;

use App\Models\User;
use App\Mail\Subscription;
use App\Traits\MailConfiguration;
use App\Events\SubscriptionEmails;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSubscriptionEmails implements ShouldQueue
{
    use MailConfiguration;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->configureMailCredentials();
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SubscriptionEmails  $event
     * @return void
     */
    public function handle(SubscriptionEmails $event)
    {
        $customerId = $event->invoice->customer;
        $customer = User::where('stripe_customer_id', $customerId)->firstOrFail();
        $event->customer = $customer;
        Mail::to($customer->email)->send(new Subscription($event));
    }
}
