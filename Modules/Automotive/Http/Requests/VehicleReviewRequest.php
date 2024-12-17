<?php

namespace Modules\Automotive\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class VehicleReviewRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'make_id' => ['required', 'exists:standard_tags,id'],
            'model_id' => ['required', 'exists:standard_tags,id'],
            'year' => ['required', 'date'],
            'status' => ['nullable'],
            'overall_rating' => ['required', 'integer', 'min:0', 'max:5'],
            'comfort' => ['required', 'integer', 'min:0', 'max:5'],
            'interior_design' => ['required', 'integer', 'min:0', 'max:5'],
            'performance' => ['required', 'integer', 'min:0', 'max:5'],
            'value_for_the_money' => ['required', 'integer', 'min:0', 'max:5'],
            'exterior_styling' => ['required', 'integer', 'min:0', 'max:5'],
            'reliability' => ['required', 'integer', 'min:0', 'max:5'],
            'title' => ['required'],
            'recommendation' => ['required'],
            'condition' => ['required'],
            'purpose' => ['required'],
            'reviewer' => ['required'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
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
