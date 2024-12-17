<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CardExpiryStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $card;
    public $customer;
    public $type;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($card, $customer, $type)
    {
        $this->card = $card;
        $this->customer = $customer;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.creditcardstatus')->subject('Credit Card Status')
            ->with('card', $this->card)->with('customer', $this->customer)->with('eventType', $this->type);
    }
}
