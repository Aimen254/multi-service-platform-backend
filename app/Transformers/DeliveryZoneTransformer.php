<?php
namespace App\Transformers;

use App\Transformers\Transformer;
use Brick\Math\BigInteger;

class DeliveryZoneTransformer extends Transformer
{

    public function transform($delivery, $options = null)
    {
        return [
            'id' => (int) $delivery->id,
            'mileage_fee' => (string) $delivery->mileage_fee,
            'extra_mileage_fee' => (string) $delivery->extra_mileage_fee,
            'mileage_distance' => (int) $delivery->mileage_distance,
            'fixed_amount' => (string) $delivery->fixed_amount,
            'percentage_amount' => (string) $delivery->percentage_amount,
            'zone_type' => (string) $delivery->zone_type,
            'fee_type' => (int) $delivery->fee_type,
            'delivery_type' => $delivery->delivery_type,
            'latitude' => $delivery->latitude,
            'longitude' => $delivery->longitude,
            'radius' => (int) $delivery->radius,
            'polygon' => $delivery->polygon,
            'address' => (string) $delivery->address,
            'platform_delivery_type' => (string) $delivery->platform_delivery_type,
        ];
    }
}
