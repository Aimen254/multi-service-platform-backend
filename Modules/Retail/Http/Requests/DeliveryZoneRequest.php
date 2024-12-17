<?php

namespace Modules\Retail\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Business\Settings\DeliveryType;
use App\Enums\Business\Settings\CustomDeliveryFee;

class DeliveryZoneRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $deliveryType = request()->input('delivery_type')
            ? DeliveryType::coerce(str_replace(' ', '', ucwords(request()->delivery_type)))->value
            : NUll;
        $feeType = request()->input('fee_type')
            ? CustomDeliveryFee::coerce(str_replace(' ', '', ucwords(request()->fee_type)))->value
            : NULL;
        return [
            'delivery_type' => 'required',
            'zone_type' => Rule::requiredIf(function () use ($deliveryType) {
                return $deliveryType ? $deliveryType == DeliveryType::SelfDelivery : false;
            }),
            'platform_delivery_type' => Rule::requiredIf(function () use ($deliveryType) {
                return $deliveryType ? $deliveryType == DeliveryType::PlatformDelivery : false;
            }),
            'fee_type' => Rule::requiredIf(function () use ($deliveryType) {
                return $deliveryType ? $deliveryType == DeliveryType::SelfDelivery : false;
            }),
            'mileage_fee' => [
                'nullable',
                Rule::requiredIf(function () use ($feeType , $deliveryType) {
                    return !\is_null($feeType)
                        ? ($deliveryType ==  DeliveryType::SelfDelivery && $feeType == CustomDeliveryFee::DeliveryFeeByMileage) : false;
                }),
                'min:0'
            ],
            'extra_mileage_fee' => [
                'nullable',
                Rule::requiredIf(function () use ($feeType , $deliveryType) {
                    return !\is_null($feeType)
                        ? ($deliveryType ==  DeliveryType::SelfDelivery && $feeType == CustomDeliveryFee::DeliveryFeeByMileage) : false;
                }),
                'min:0'
            ],
            'mileage_distance' => [
                'nullable',
                Rule::requiredIf(function () use ($feeType , $deliveryType) {
                    return !\is_null($feeType)
                        ? ($deliveryType ==  DeliveryType::SelfDelivery && $feeType == CustomDeliveryFee::DeliveryFeeByMileage) : false;
                }),
                'min:0'
            ],
            'fixed_amount' => [
                'nullable',
                Rule::requiredIf(function () use ($feeType , $deliveryType) {
                    return !\is_null($feeType)
                        ? ($deliveryType ==  DeliveryType::SelfDelivery && $feeType == CustomDeliveryFee::DeliveryFeeAsPercentageOfSale) : false;
                }),
                'min:0'
            ],
            'percentage_amount' => [
                'nullable',
                Rule::requiredIf(function () use ($feeType , $deliveryType) {
                    return !\is_null($feeType)
                        ? ($deliveryType ==  DeliveryType::SelfDelivery && $feeType == CustomDeliveryFee::DeliveryFeeAsPercentageOfSale) : false;
                }),
                'integer',
                'min:0'
            ]
        ];    
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }
}
