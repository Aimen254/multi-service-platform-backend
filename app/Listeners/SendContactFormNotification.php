<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Product;
use App\Traits\MailConfiguration;
use Illuminate\Support\Facades\Log;
use App\Events\ContactFormProcessed;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Automotive\Emails\SendContactFormEmail;

class SendContactFormNotification implements ShouldQueue
{
    //
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
     * @param  \App\Events\ContactFormProcessed  $event
     * @return void
     */
    public function handle(ContactFormProcessed $event)
    {
        $product = Product::where('id', $event->contactForm->product_id)->with(['business.businessOwner', 'user'])->first();
        Mail::to($product->business ? $product->business->businessOwner : $product->user->email)->send(new SendContactFormEmail($product, $event->contactForm, $event->module));
    }
}
