<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OrderStatusEmail
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $order;
    public $type;
    public $businessOwner;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($order, $type, $businessOwner = null)
    {
        $this->order = $order;
        $this->type = $type;
        $this->businessOwner = $businessOwner;
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
