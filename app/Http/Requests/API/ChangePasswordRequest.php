<?php

namespace App\Http\Requests\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ChangePasswordRequest extends FormRequest
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
        switch(auth()->check()) {
            case false:
                return [
                    'otp' => 'required|numeric|digits_between:6,6',
                    'new_password' => 'required|min:8',
                    'password_confirmation'=>'required|min:8|same:new_password',
                ];
            case true:
                return [
                    'old_password' => [
                        'required', function ($attribute, $value, $fail) {
                            if (!Hash::check($value, auth()->user()->password)) {
                                $fail('Old Password didn\'t match');
                            }
                        },
                    ],
                'new_password' => 'required|min:8',
                'password_confirmation'=>'required|min:8|same:new_password',
            ];
        }

    }

    public function messages()
    {
        return [
            'otp.digits_between' => "Otp code must be of 6 digits",
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            'message' => $validator->errors()
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY,);

        throw new ValidationException($validator, $response);
    }
}
