<?php
namespace App\Transformers;

use stdClass;
use App\Transformers\Transformer;

class OrderTrackTransformer extends Transformer
{

    public function transform($orderTrack, $options = null)
    {
        $orderTrackResponse = [
            'id' => $orderTrack ? (int)$orderTrack->id : null,
            'status' => $orderTrack ? (new OrderStatusTransformer)->transform($orderTrack->orderStatus) : [],
            'date' => $orderTrack ? timeFormat($orderTrack->created_at) : null,
        ];
        return $orderTrackResponse;
    }
}
