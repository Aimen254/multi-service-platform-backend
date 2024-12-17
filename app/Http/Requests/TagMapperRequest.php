<?php

namespace App\Http\Requests;

use App\Rules\MultiProductTagsMappingRule;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TagMapperRequest extends FormRequest
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
        return [
            'level_one_tag' => ['nullable', Rule::requiredIf(fn () => request()->global_tag), new MultiProductTagsMappingRule],
            'level_two_tag' => ['nullable', Rule::requiredIf(fn() => request()->level_one_tag && count(request()->level_one_tag) > 1 && request()->product_tag_level_two_avalable == true)]
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'level_one_tag.required' => 'Level one product tag id field is required.',
        ];
    }
}
