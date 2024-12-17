<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
        switch (request()->input('group')) {
            case 'stripe_connect_settings':
            case 'number_format_settings':
            case 'time_format_settings':
            case 'tax_model_settings':
            case 'driver_assignment_settings':
            case 'email_notification':
            case 'social_authentication':
                return [
                    'settings.*.value' => 'required'
                ];
                break;
            case 'push_notification':
                if(!is_string(request()->settings[1]['value'])){
                    return [
                        'settings.1.value' => 'file|mimetypes:application/json'
                    ];
                    break;
                }
        }
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'settings.*.value.required' => 'This field is required.',
            'settings.1.value.mimetypes' => 'The requested file must be a valid json.',
            'settings.1.value.file' => 'This field must be a file.'
        ];
    }
}
