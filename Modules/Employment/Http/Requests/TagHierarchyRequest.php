<?php

namespace Modules\Employment\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TagHierarchyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'L2' => 'required',
            'L3' => [Rule::requiredIf(function () {
                return $this->route('level') > 3;
            })],
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

    public function messages()
    {
        return [
            'L2.required' => 'Level 2 tag is required',
            'L3.required' => 'Level 3 tags is required',
        ];
    }
}
