<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Business\Settings\CustomDeliveryFee;


class NewsPaperDeliveryZoneRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $feeType = request()->input('fee_type')
            ? CustomDeliveryFee::coerce(str_replace(' ', '', ucwords(request()->fee_type)))->value
            : NULL;
        if(request()->input('location')){
            return [
                'zone_type' => 'required',
                'address' => 'required',
                'polygon' => 'required_if:zone_type,polygon',
                'radius'=>'required_if:zone_type,circle',
            ];
        }
        return [
            'fee_type' => 'required',
            'mileage_fee' => [
                'nullable',
                Rule::requiredIf(function () use ($feeType) {
                    return !\is_null($feeType)
                        ? $feeType == CustomDeliveryFee::DeliveryFeeByMileage : false;
                }),
                'integer',
                'min:0'
            ],
            'extra_mileage_fee' => [
                'nullable',
                Rule::requiredIf(function () use ($feeType) {
                    return !\is_null($feeType)
                        ? $feeType == CustomDeliveryFee::DeliveryFeeByMileage : false;
                }),
                'integer',
                'min:0'
            ],
            'mileage_distance' => [
                'nullable',
                Rule::requiredIf(function () use ($feeType) {
                    return !\is_null($feeType)
                        ? $feeType == CustomDeliveryFee::DeliveryFeeByMileage : false;
                }),
                'integer',
                'min:0'
            ],
            'fixed_amount' => [
                'nullable',
                Rule::requiredIf(function () use ($feeType) {
                    return !\is_null($feeType)
                        ? $feeType == CustomDeliveryFee::DeliveryFeeAsPercentageOfSale : false;
                }),
                'integer',
                'min:0'
            ],
            'percentage_amount' => [
                'nullable',
                Rule::requiredIf(function () use ($feeType) {
                    return !\is_null($feeType)
                        ? $feeType == CustomDeliveryFee::DeliveryFeeAsPercentageOfSale : false;
                }),
                'integer',
                'min:0'
            ]
        ];
    }

    public function messages()
    {
        return [
            'radius.required_if' => 'Please draw circle on map',
            'polygon.required_if' => 'Please draw polygon on map',
        ];
    }
}
