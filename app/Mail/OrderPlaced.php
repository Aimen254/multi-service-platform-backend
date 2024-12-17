<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced extends Mailable
{
    use Queueable, SerializesModels;
    public $order;
    public $user;
    public $businessEmails;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $user, $businessEmails = false)
    {
        $this->order = $order;
        $this->user = $user;
        $this->businessEmails = $businessEmails;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->businessEmails) {
            return $this->markdown('mail.orderplacedemailToBusinessOwner')->subject('Order Placed')
            ->with('user', $this->user)->with('order', $this->order);
        } else {
            return $this->markdown('mail.orderplacedemail')->subject('Order Placed')
            ->with('user', $this->user)->with('order', $this->order);
        }
    }
}
