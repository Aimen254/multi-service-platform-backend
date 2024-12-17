<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderStatus extends Mailable
{
    use Queueable, SerializesModels;
    public $order;
    public $user;
    public $type;
    public $businessOwner;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $user, $type, $businessOwner)
    {
        $this->order = $order;
        $this->user = $user;
        $this->type = $type;
        $this->businessOwner = $businessOwner;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $emailTemplate = 'mail.orderstatusemail';
        return $this->view($emailTemplate)->subject('Order Status Changed')
            ->with('user', $this->user)->with('order', $this->order)->with('eventType', $this->type)->with('business_owner', $this->businessOwner)->with('mail_to_business_owner', false);
    }
}
