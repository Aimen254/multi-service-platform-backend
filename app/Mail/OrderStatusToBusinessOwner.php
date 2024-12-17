<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusToBusinessOwner extends Mailable
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
            ->with('user', $this->user)->with('order', $this->order)->with('business_owner', $this->businessOwner)->with('eventType', $this->type)->with('mail_to_business_owner', true);
    }
}
