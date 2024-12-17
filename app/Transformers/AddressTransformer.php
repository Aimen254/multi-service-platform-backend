<?php
namespace App\Transformers;

use App\Transformers\Transformer;

class AddressTransformer extends Transformer
{

    public function transform($address, $options = null)
    {
        return [
            'id' => (int) $address->id,
            'name' => (string) $address->name,
            'address' => (string) $address->address,
            'street_address' => (string) $address->street_address,
            'latitude' => $address->latitude,
            'longitude' => $address->longitude,
            'note' => (string) $address->note,
            'status' => (string) $address->status,
            'is_default' => (boolean) $address->is_default,
            'type' => (string) $address->type,
            'zipcode'=>(string) $address->zipcode,
        ];
    }
}
