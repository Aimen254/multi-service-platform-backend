<?php

namespace Modules\Retail\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'settings.0.value' => 'nullable|integer|min:0',
            'settings.2.value' => 'required|integer|max:100|min:0',
            'settings.3.value' => 'nullable|integer|max:100|min:0',
            'settings.5.value' => 'required|integer|max:100|min:0',

        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \auth()->check();
    }

    protected function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();
        $validator->sometimes('settings.5.value', 'required|integer|max:100|min:0', function ($input) {
            return $input->settings[4]['value'] == 'Platform fee in percentage';
        });
        return $validator;
    }


    public function messages()
    {
        return [
            'settings.2.value.required' => 'Tax percantage field is required',
            'settings.5.value.required' => 'Custom plateform fee field is required',
            'settings.0.value.min' => 'Minimum value must not be less than 0',
            'settings.2.value.min' => 'Minimum value must not be less than 0',
            'settings.2.value.max' => 'Maximum value must not be greater than 100',
            'settings.3.value.min' => 'Minimum value must not be less than 0',
            'settings.3.value.max' => 'Maximum value must not be greater than 100',
            'settings.5.value.min' => 'Minimum value must not be less than 0',
            'settings.5.value.max' => 'Maximum value must not be greater than 100',

        ];
    }
}
