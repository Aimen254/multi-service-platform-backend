<?php
namespace App\Transformers;

use App\Transformers\Transformer;
use App\Transformers\UserTransformer;
use stdClass;

class CreditCardTransformer extends Transformer
{

    public function transform($card, $options = null)
    {
        return [
            'id' => $card ?  (int) $card->id : null,
            'user_name' => $card ? (string) $card->user_name : '',
            'payment_method_id' => $card ? (string) $card->payment_method_id : '',
            'expiry_month' => $card ? (string) $card->expiry_month : '',
            'expiry_year' => $card ? (string) $card->expiry_year : '',
            'last_four' => $card ? (string) $card->last_four : '',
            'brand' => $card ? (string) $card->brand : '',
            'save_card' => $card ? (int) $card->save_card : '',
            'default' => $card ? (int) $card->default : '',
            'user' => $card ? (new UserTransformer)->transform($card->user) : new stdClass(),
        ];
    }
}
