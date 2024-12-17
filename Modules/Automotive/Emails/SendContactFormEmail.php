<?php

namespace Modules\Automotive\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendContactFormEmail extends Mailable
{
    public $product;
    public $contactForm;
    public $module;
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($product, $contactForm, $module)
    {
        $this->product = $product;
        $this->contactForm = $contactForm;
        $this->module = $module;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = ucfirst(str_replace('_', ' ', $this->contactForm->subject) . ' for ' . $this->product->name);
        return $this->markdown('mail.contactformemail')->subject($subject)
            ->with('contact', $this->contactForm)->with('product', $this->product)->with('module', $this->module);
    }
}
