<?php

namespace Modules\Boats\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Propaganistas\LaravelPhone\PhoneNumber;

class ContactFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // dd(request()->all());
        // phone(request()->phone)->formatE164();
        $module = request()->module;
     

        switch (request()->getMethod()) {
            case 'POST':
                return [
                    'first_name' => ['required'],
                    'last_name' => ['required'],
                    'email' => ['required'],
                    'phone' => ['required', 'regex:/^\+\d{1,3}\d{9,}$/'],
                    'subject' => ['required'],
                    'comment' => ['required'],
                    'trade_in' => ['nullable'],
                    'user_id' => ['required', 'exists:users,id'],
                    'product_id' => ['required', 'exists:products,id'],
                    'is_urgent' => ['nullable', Rule::requiredIf($module === 'taskers' || $module === 'services')]
                ];
            case 'PUT':
                return [
                    'first_name' => ['required'],
                    'last_name' => ['required'],
                    'email' => ['required'],
                    'phone' => ['required','regex:/^\+\d{1,3}\d{9,}$/'],
                    'subject' => ['required'],
                    'comment' => ['required'],
                    'trade_in' => ['nullable'],
                    'user_id' => ['required', 'exists:users,id'],
                    'product_id' => ['required', 'exists:products,id'],
                    'is_urgent' => ['nullable', Rule::requiredIf($module === 'taskers' || $module === 'services')]
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
            'message' => $validator->errors()
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
