<?php

namespace App\Http\Requests;

use App\Models\Business;
use Illuminate\Foundation\Http\FormRequest;;

class BusinessRequest extends FormRequest
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
        switch (request()->getMethod()) {
            case 'POST':
                return [
                    'name' => 'required',
                    'slug' => ['required', 'without_spaces', 'unique:businesses,slug'],
                    'email' => ['required'],
                    'owner_id' => 'required',
                    'address' => ['nullable'],
                    'module' => 'required'
                ];
            case 'PUT':
                $businessUuid = $this->route('business');
                $businessId = getBusinessDetails($businessUuid)->id;
                switch (request()->input('group')) {
                    case 'business_info':
                        return [
                            'name' => 'required',
                            'slug' => [
                                'required',
                                'without_spaces',
                                'unique:businesses,slug,' . $businessId
                            ],
                            'email' => ['required'],
                            'owner_id' => 'required',
                            'address' => ['nullable'],
                            'phone' => ['nullable','phone'],
                            'shipping_and_return_policy' => 'required|max:3000',
                            'shipping_and_return_policy_short' => 'nullable|max:1500',
                            'long_description' => 'nullable|max:2500',
                            'short_description' => 'nullable|max:1000',
                            'message' => 'nullable|max:500'
                        ];
                    case 'business_settings':
                        return [
                            'minimum_purchase' => 'required|integer'
                        ];
                }
        }
    }

    public function messages()
    {
        return [
            'slug.without_spaces' => 'The :attribute can not contains spaces.'
        ];
    }
}