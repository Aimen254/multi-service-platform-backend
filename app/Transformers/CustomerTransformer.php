<?php
namespace App\Transformers;

use App\Transformers\Transformer;

class CustomerTransformer extends Transformer
{
    public function transform($customer, $options = null)
    {
        $data =  [
            'first_name' => $customer->first_name,
            'last_name'=>$customer->last_name,
            'email' => $customer->email,
            'avatar' => (string) getImage($customer->avatar, 'avatar', $customer->is_external),
            'phone' => $customer->phone,
        ];
        return $data;
    }
}
