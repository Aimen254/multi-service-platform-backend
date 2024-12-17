<?php
namespace App\Transformers;

use App\Transformers\Transformer;

class CouponTransformer extends Transformer
{
    public function transform($coupon, $options = null)
    {
        $data = [
            'id' => $coupon?->id,
            'text' => (string) $coupon->code,
        ];

        if(isset($options['store_cpupon']) && $options['store_cpupon']) {
            $data['model_type'] = $coupon?->model_type;
            $data['model_id'] = $coupon?->model_id;
            $data['minimum_purchase'] = $coupon?->minimum_purchase;
            $data['limit'] = $coupon?->limit;
            $data['discount_type'] = $coupon?->discount_type;
            $data['discount_value'] = $coupon?->discount_value;
            $data['start_date'] = $coupon?->start_date;
            $data['end_date'] = $coupon?->end_date;
            $data['coupon_type'] = $coupon?->coupon_type;
            $data['status'] = $coupon?->status;
            $data['created_by'] = $coupon?->created_by;
            $data['created_at'] = $coupon?->created_at;
        }
        return $data;
    }
}
