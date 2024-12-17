<?php

namespace App\Transformers;

use stdClass;
use App\Transformers\Transformer;
use App\Transformers\UserTransformer;
use App\Transformers\ProductTransformer;

class EventBookingTransformer extends Transformer
{
    public function transform($booking, $options = null)
    {
        $data = [
            'id' => (int) $booking?->id,
            'status' => (string) $booking?->status,
            'ticket_price' => $booking?->ticket_price,
            'user' => $booking?->user ? (new UserTransformer)->transform($booking?->user) : new stdClass,
            'product' => $booking?->product ? (new ProductTransformer)->transform($booking?->product) : new stdClass
        ];
        return $data;
    }
}
