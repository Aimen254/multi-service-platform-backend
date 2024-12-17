<?php

namespace App\Listeners;

use App\Mail\OrderStatus;
use App\Events\OrderStatusEmail;
use App\Traits\MailConfiguration;
use Illuminate\Support\Facades\Mail;
use App\Models\Business;
use App\Mail\OrderStatusToBusinessOwner;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Retail\Entities\BusinessAdditionalEmail;

class OrderStatusNotificationEmail implements ShouldQueue
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
     * @param  object  $event
     * @return void
     */
    public function handle(OrderStatusEmail $event)
    {
        //adding all additional emails and business owners email in an array
        $allEmails = [];
        $emails = BusinessAdditionalEmail::where('business_id', $event->order->business_id)->select('email')->get()->toArray();
        if ($emails) {
            foreach ($emails as $key => $value) {
                $allEmails[$key] = $value['email'];
            }
        }
        array_push($allEmails, $event->businessOwner->email);
        //sending email to business owner and to additional emails
        foreach ($allEmails as $key => $value) {
            Mail::to($value)->send(new OrderStatusToBusinessOwner($event->order, $event->order->model, $event->type, $event->businessOwner));
        }
        //sending email to customer
        Mail::to($event->order->model->email)->send(new OrderStatus($event->order, $event->order->model, $event->type, $event->businessOwner));
    }
}
