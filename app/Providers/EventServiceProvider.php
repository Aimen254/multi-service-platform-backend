<?php

namespace App\Providers;

use App\Events\CardExpiryStatus;
use App\Events\OrderPlacedEmail;
use App\Events\OrderStatusEmail;
use App\Events\SubscriptionEmails;
use App\Events\UpdateBookedEvents;
use App\Events\ForgotPasswordEmail;
use App\Events\ContactFormProcessed;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Listeners\SendSubscriptionEmails;
use App\Events\SendEmailVerificationEvent;
use App\Listeners\SendForgotPasswordEmail;
use App\Listeners\UpdateEventsBookingTime;
use App\Listeners\SendContactFormNotification;
use App\Events\ForgotPasswordEmailNotification;
use App\Listeners\CardExpiryStatusNotification;
use App\Listeners\OrderStatusNotificationEmail;
use App\Listeners\SendEmailVerificationListener;
use App\Listeners\OrderPlacementEmailNotification;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SendEmailVerificationEvent::class => [
            SendEmailVerificationListener::class,
        ],
        ForgotPasswordEmailNotification::class => [
            SendForgotPasswordEmail::class,
        ],
        OrderPlacedEmail::class => [
            OrderPlacementEmailNotification::class,
        ],
        OrderStatusEmail::class => [
            OrderStatusNotificationEmail::class,
        ],
        CardExpiryStatus::class => [
            CardExpiryStatusNotification::class,
        ],
        SubscriptionEmails::class => [
            SendSubscriptionEmails::class,
        ],
        ContactFormProcessed::class => [
            SendContactFormNotification::class
        ],
        UpdateBookedEvents::class => [
            UpdateEventsBookingTime::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
