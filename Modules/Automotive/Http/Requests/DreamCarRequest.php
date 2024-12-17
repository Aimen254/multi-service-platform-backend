<?php

namespace Modules\Automotive\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class DreamCarRequest extends FormRequest
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
                    'module' => ['required', 'exists:standard_tags,slug'],
                    'make_id' => ['required', 'exists:standard_tags,id'],
                    'model_id' => ['required', 'exists:standard_tags,id'],
                    'from' => 'required',
                    'to' => 'required'
                ];
            case 'PUT':
                return [
                    'module' => ['required', 'exists:standard_tags,slug'],
                    'make_id' => ['required', 'exists:standard_tags,id'],
                    'model_id' => ['required', 'exists:standard_tags,id'],
                    'from' => 'required',
                    'to' => 'required'
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
}
