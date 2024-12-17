<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactFormProcessed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $contactForm;
    public $module;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($contactForm, $module)
    {
        $this->contactForm = $contactForm;
        $this->module = $module;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
