<?php

namespace App\Listeners;

use App\Mail\VerificationEmail;
use App\Traits\MailConfiguration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\SendEmailVerificationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmailVerificationListener implements ShouldQueue
{
    use MailConfiguration;
    public $tries = 3;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->configureMailCredentials();
        Log::alert(Config::get('mail.mailers.smtp'));
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SendEmailVerificationEvent  $event
     * @return void
     */
    public function handle(SendEmailVerificationEvent $event)
    {
        Log::info(Config::get('mail.mailers.smtp'));
        Mail::to($event->user->email)->send(new VerificationEmail($event->user, $event->otp));
    }
}
