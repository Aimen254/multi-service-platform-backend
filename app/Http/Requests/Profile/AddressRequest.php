<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
        return [
            'addresses.*.name' => 'required',
            'addresses.*.address' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'addresses.*.name.required' => 'Name field is required',
            'addresses.*.address.required' => 'Address field is required',
        ];
    }
}
