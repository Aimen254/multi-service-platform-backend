<?php

namespace Modules\Automotive\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Automotive\Rules\CheckDiscountPrice;

class VehicleDiscountRequest extends FormRequest
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
            case 'PUT':
                return [
                    'discount_start_date' =>  ['nullable', 'date', 'after:yesterday'],
                    'discount_end_date' =>  ['nullable', 'date', 'after:yesterday', 'after:discount_start_date'],
                    'discount_value' => new CheckDiscountPrice(),
                ];
        }
    }

    protected function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();
        $validator->sometimes('discount_price', 'nullable|integer|max:100|min:0', function ($input) {
            return $input->discount_type == 'percentage';
        });
        return $validator;
    }
}
