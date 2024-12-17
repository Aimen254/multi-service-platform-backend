<?php
namespace App\Transformers;

use App\Transformers\Transformer;

class OrderStatusTransformer extends Transformer
{

    public function transform($orderStatus, $options = null)
    {
        $orderStatusResponse = [
            'id' => $orderStatus ? (int)$orderStatus->id : null,
            'name' => $orderStatus ? (string) $orderStatus->status : '',
        ];
        return $orderStatusResponse;
    }
}
