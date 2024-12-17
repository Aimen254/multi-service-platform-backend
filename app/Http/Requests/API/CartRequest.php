<?php

namespace App\Http\Requests\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CartRequest extends FormRequest
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
                    'product_uuid' => ['required', 'exists:products,uuid'],
                    'quantity' => ['required', 'integer', 'min:1']
                ];

            case 'PUT':
                return [
                    'item_uuid' => ['required', 'exists:cart_items,uuid'],
                    'quantity' => ['required', 'integer', 'min:1']
                ];
            case "DELETE":
                return [
                    'item_uuid' => ['nullable', 'exists:cart_items,uuid'],
                ];
        }
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
