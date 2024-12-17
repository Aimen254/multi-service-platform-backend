<?php
namespace App\Transformers;

use App\Transformers\Transformer;

class CouponsListTransformer extends Transformer
{
    /**
     * @param $coupon
     * @param $options
     * @return array
     */
    public function transform($coupon, $options = null)
    {
        return [
            'id' => $coupon->id,
            'code' => (string) $coupon->code,
            'discount_value' => numberFormat($coupon->discount_value),
            'discount_type' => (string) $coupon->discount_type,
            'status' => (string) $coupon->status,
            'type' => (string) $coupon->coupon_type,
            'expiry' => (string) $coupon->end_date,
        ];
    }
}
