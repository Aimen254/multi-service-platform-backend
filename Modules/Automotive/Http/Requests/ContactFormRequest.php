<?php

namespace Modules\Automotive\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ContactFormRequest extends FormRequest
{
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
                    'first_name' => ['required'],
                    'last_name' => ['required'],
                    'email' => ['required'],
                    'phone' => ['nullable'],
                    'subject' => ['required'],
                    'comment' => ['required', 'max:500'],
                    'trade_in' => ['nullable'],
                    'user_id' => ['required', 'exists:users,id'],
                    'product_id' => ['required', 'exists:products,id'],
                ];
            case 'PUT':
                return [
                    'first_name' => ['required'],
                    'last_name' => ['required'],
                    'email' => ['required'],
                    'phone' => ['nullable'],
                    'subject' => ['required'],
                    'comment' => ['required'],
                    'trade_in' => ['nullable'],
                    'user_id' => ['required', 'exists:users,id'],
                    'product_id' => ['required', 'exists:products,id'],
                ];
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            'message' => $validator->errors()->first()
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY,);
        throw new ValidationException($validator, $response);
    }

    public function prepareForValidation()
    {
        $this->merge([
            'user_id' => $this->user()->id ?? $this->user()->id,
        ]);
    }
}
