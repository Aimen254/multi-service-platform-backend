<?php
namespace App\Transformers;

use App\Transformers\Transformer;

class MailingTransformer extends Transformer
{

    public function transform($mailing, $options = null)
    {
        return [
            'id' => (int) $mailing->id,
            'title' => (string) $mailing->title,
            'minimum_amount' => numberFormat($mailing->minimum_amount),
            'price' => numberFormat($mailing->price),
            'status' => (string) $mailing->status
        ];
    }
}
