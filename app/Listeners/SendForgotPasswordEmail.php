<?php

namespace App\Listeners;

use App\Mail\ForgetPasswordEmail;
use App\Traits\MailConfiguration;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\ForgotPasswordEmailNotification;

class SendForgotPasswordEmail implements ShouldQueue
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
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ForgotPasswordEmailNotification  $event
     * @return void
     */
    public function handle(ForgotPasswordEmailNotification $event)
    {
        Mail::to($event->user->email)->send(new ForgetPasswordEmail($event->user, $event->otp));
    }
}
