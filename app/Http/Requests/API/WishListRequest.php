<?php

namespace App\Http\Requests\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class WishListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('sanctum')->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'nullable|in:business,product,tag,user',
            'product_id' => 'required_if:type,product|exists:products,id',
            'module_id' => 'required_if:type,user|exists:standard_tags,id',
            'business_id' => 'required_if:type,business|exists:businesses,id',
            'tag_id' => 'required_if:type,tag|exists:standard_tags,id',
            'tag_type' => 'required_if:type,tag',
            'user_id' => 'required_if:type,user|exists:users,id'
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
