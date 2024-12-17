<?php

namespace App\Http\Requests\Api;

use App\Rules\CheckUserType;
use Illuminate\Http\JsonResponse;
use App\Rules\CheckDeactivatedEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ForgotPasswordRequest extends FormRequest
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
            'email' => ['required','email', 'exists:users', new CheckDeactivatedEmail(), new CheckUserType()]
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([ 
            'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            'message' => $validator->errors()->first()
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY,);

        throw new ValidationException($validator, $response);
    }
}
