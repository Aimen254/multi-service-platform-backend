<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreditCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_name' => ['required'],
            'user_id' => ['nullable'],
            'email' => ['nullable'],
            'payment_method_id' => ['required'],
            'brand' => ['nullable'],
            'country' => ['nullable'],
            'expiry_month' => ['required'],
            'expiry_year' => ['required'],
            'last_four' => ['required'],
            'live_mode' => ['nullable'],
            'token' => ['nullable'],
            'customer_id' => ['nullable']
        ];
    }
}
