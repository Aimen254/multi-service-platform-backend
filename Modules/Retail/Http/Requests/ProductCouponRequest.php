<?php

namespace Modules\Retail\Http\Requests;

use App\Rules\CheckCouponDiscountPrice;
use Illuminate\Foundation\Http\FormRequest;

class ProductCouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch (request()->getMethod()) {
            case 'POST':
                return [
                    'coupon_id' => [
                        'required',
                        'exists:coupons,id',
                        new CheckCouponDiscountPrice()
                    ],
                ];
        }
    }
}
