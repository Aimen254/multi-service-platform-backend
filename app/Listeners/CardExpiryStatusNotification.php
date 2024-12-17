<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\CardExpiryStatus;
use Illuminate\Support\Facades\Mail;
use App\Mail\CardExpiryStatusMail;

class CardExpiryStatusNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CardExpiryStatus $event)
    {
        $card = $event->card;
        $customer = $event->customer;
        //sending email to customer
        Mail::to($customer->email)->send(new CardExpiryStatusMail($card, $customer, $event->type));
    }
}
