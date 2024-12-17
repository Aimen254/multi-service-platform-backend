<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateBookedEvents
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var mixed
     */
    public $product;
    public $date;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($product, $event_date)
    {
        $this->product = $product;
        $this->date = $event_date;
    }
}
