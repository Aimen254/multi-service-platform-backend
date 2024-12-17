<?php
namespace App\Transformers;

use App\Transformers\Transformer;

class RecepientTransformer extends Transformer
{

    public function transform($recepient, $options = null)
    {
        return [
            'id' => (int) $recepient->id,
            'first_name' => (string) $recepient->first_name,
            'last_name' => (string) $recepient->last_name,
            'email' => (string) $recepient->email,
            'phone' => $recepient->phone,
        ];
    }
}
