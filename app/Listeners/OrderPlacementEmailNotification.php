<?php

namespace App\Listeners;

use App\Mail\OrderPlaced;
use App\Events\OrderPlacedEmail;
use App\Traits\MailConfiguration;
use Illuminate\Support\Facades\Mail;
use App\Models\Business;;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Modules\Retail\Entities\BusinessAdditionalEmail;

class OrderPlacementEmailNotification implements ShouldQueue
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
     * @param  \App\Events\OrderPlacedEmail  $event
     * @return void
     */
    public function handle(OrderPlacedEmail $event)
    {
        //adding all additional emails and business owners email in an array 
        $allEmails = [];
        $emails = BusinessAdditionalEmail::where('business_id', $event->order->business_id)->select('email')->get()->toArray();
        if ($emails) {
            foreach ($emails as $key => $value) {
                $allEmails[$key] = $value['email'];
            }
        }
        //getting business owners email
        $businessOwnerEmail = Business::where('id', $event->order->business_id)->first()->businessOwner->email;
        array_push($allEmails, $businessOwnerEmail);
        //sending email to business owner and to additional emails
        foreach ($allEmails as $key => $value) {
            Mail::to($value)->send(new OrderPlaced($event->order, $event->order->model, true));
        }
        Mail::to($event->order->model->email)->send(new OrderPlaced($event->order, $event->order->model));
    }
}
