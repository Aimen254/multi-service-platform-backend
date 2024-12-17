<?php

namespace App\Http\Requests;

use App\Models\StandardTag;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class DreamProductRequest extends FormRequest
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
        $module = StandardTag::where('id', request()->module)->orWhere('slug', request()->module)->first()->slug;
        switch (request()->getMethod()) {
            case 'POST':
                return [
                    'make_id' => ['required', 'exists:standard_tags,id'],
                    'model_id' => ['required', 'exists:standard_tags,id'],
                    'level_four_tag_id' => ['nullable', Rule::requiredIf(!in_array($module, ['boats', 'automotive'])), 'exists:standard_tags,id'],
                    'from' => ['nullable', Rule::requiredIf(in_array($module, ['boats', 'automotive']))],
                    'to' => ['nullable', Rule::requiredIf(in_array($module, ['boats', 'automotive']))],
                    'min_price' => ['nullable', Rule::requiredIf(in_array($module, ['real-estate'])), 'numeric', 'min:0'],
                    'max_price' => ['nullable', Rule::requiredIf(in_array($module, ['real-estate'])), 'numeric', 'min:0'],
                    'bed' => ['nullable', Rule::requiredIf(in_array($module, ['real-estate']))],
                    'bath' => ['nullable', Rule::requiredIf(in_array($module, ['real-estate']))],
                    // 'square_feet' => ['nullable', Rule::requiredIf(in_array($module, ['real-estate']))]
                ];
            case 'PUT':
                return [
                    'make_id' => ['required', 'exists:standard_tags,id'],
                    'model_id' => ['required', 'exists:standard_tags,id'],
                    'level_four_tag_id' => ['nullable', Rule::requiredIf(!in_array($module, ['boats', 'automotive'])), 'exists:standard_tags,id'],
                    'from' => ['nullable', Rule::requiredIf(in_array($module, ['boats', 'automotive']))],
                    'to' => ['nullable', Rule::requiredIf(in_array($module, ['boats', 'automotive']))],
                    'min_price' => ['nullable', Rule::requiredIf(in_array($module, ['real-estate'])), 'numeric', 'min:0'],
                    'max_price' => ['nullable', Rule::requiredIf(in_array($module, ['real-estate'])), 'numeric', 'min:0'],
                    'bed' => ['nullable', Rule::requiredIf(in_array($module, ['real-estate']))],
                    'bath' => ['nullable', Rule::requiredIf(in_array($module, ['real-estate']))],
                    // 'square_feet' => ['nullable', Rule::requiredIf(in_array($module, ['real-estate']))]
                ];
        }
    }


    public function messages()
    {
        return [
            'make_id.required' => 'level two tag is required',
            'model_id.required' => 'level three tag is required',
            'level_four_tag_id.required' => 'level four tag is required',
            'min_price.required' => 'Minimum price is required',
            'max_price.required' => 'Maximum price is required',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->input('min_price') && $this->input('max_price')) {
            $min_price = $this->input('min_price');
            $max_price = $this->input('max_price');

            // Remove commas and update the input value
            $this->merge([
                'min_price' => floatval(str_replace(',', '', $min_price)),
                'max_price' => floatval(str_replace(',', '', $max_price)),
            ]);
        }
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
