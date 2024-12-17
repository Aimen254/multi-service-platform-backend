<?php

namespace App\Http\Requests;
use App\Rules\CheckPercentage;
use Illuminate\Foundation\Http\FormRequest;

class BusinessSettingRequest extends FormRequest
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
        foreach (request()->settings as $key => $value) {
            if ($value['key'] == 'minimum_purchase') {
                $min_pur_index = $key;
            }
            if ($value['key'] == 'global_price') {
                $global_price_index = $key;
            }
            if ($value['key'] == 'tax_percentage') {
                $tax_per_index = $key;
            }
        };
        
        return [
            'businessUrl' => 'nullable|url',
            'settings.'.$min_pur_index.'.value' => 'nullable|integer|min:0',
            'settings.'.$global_price_index.'.value' => 'nullable|integer|max:100|min:0',
            'settings.'.$tax_per_index.'.value' => 'nullable|integer|max:100|min:0',
        ];
    }

    public function messages()
    {
        foreach (request()->settings as $key => $value) {
            if ($value['key'] == 'minimum_purchase') {
                $min_pur_index = $key;
            }
            if ($value['key'] == 'global_price') {
                $global_price_index = $key;
            }
            if ($value['key'] == 'tax_percentage') {
                $tax_per_index = $key;
            }
        };

        return [
            'settings.'.$min_pur_index.'.value.min' => 'Minimum value must no be less than 0',
            'settings.'.$tax_per_index.'.value.min' => 'Value must not be greater than 100 and lesser than 0.',
            'settings.'.$tax_per_index.'.value.max' => 'Value must not be greater than 100 and lesser than 0.',
            'settings.'.$global_price_index.'.value.min' => 'Value must not be greater than 100 and lesser than 0.',
            'settings.'.$global_price_index.'.value.max' => 'Value must not be greater than 100 and lesser than 0.',
        ];
    }
}
