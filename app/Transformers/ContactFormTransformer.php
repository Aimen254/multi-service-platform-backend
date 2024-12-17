<?php

namespace App\Transformers;

use stdClass;
use App\Transformers\Transformer;
use App\Transformers\UserTransformer;
use App\Transformers\ProductTransformer;

class ContactFormTransformer extends Transformer
{
    public function transform($contactForm, $options = null)
    {

        $data = [
            'id' => (int) $contactForm->id,
            'first_name' => (string) $contactForm->first_name,
            'last_name' => (string) $contactForm->last_name,
            'email' => (string) $contactForm->email,
            'phone' => (string) $contactForm->phone,
            'subject' => (string) $contactForm->subject,
            'comment' => (string) $contactForm->comment,
            'trade_in' => (string) $contactForm->trade_in,
            'is_urgent' => (bool) $contactForm?->is_urgent,
            'user' => $contactForm->relationLoaded('user') && $contactForm->user ? (new UserTransformer)->transform($contactForm->user) : new stdClass(),
            'product' => $contactForm->relationLoaded('product') && $contactForm->product ? (new ProductTransformer)->transform($contactForm->product) : new stdClass(),
            'created_at' => timeFormat($contactForm->created_at)
        ];

        return $data;
    }
}
