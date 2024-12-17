<?php

namespace App\Http\Requests;

use App\Models\StandardTag;
use App\Rules\CheckLevelExistance;
use App\Rules\CheckSearchLevels;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class SearchRequest extends FormRequest
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
            'keyword' => ['required', 'min:2'],
            'L1' => ['nullable', new CheckLevelExistance(), new CheckSearchLevels()],
            'L2' => ['nullable', new CheckLevelExistance(), new CheckSearchLevels()],
            'L3' => ['nullable', new CheckLevelExistance(), new CheckSearchLevels()]
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
