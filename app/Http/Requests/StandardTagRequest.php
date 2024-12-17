<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StandardTagRequest extends FormRequest
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
        $name = request()->input('name');
        $type = request()->input('type');
        switch (request()->getMethod()) {
            case 'POST':
                return [
                    'name' => [
                        'required',
                        Rule::unique('standard_tags')->where(function ($query) use($name,$type) {
                            return $query->where('name', $name)
                            ->where('type', $type);
                        }),
                    ],
                    'parent_id' => 'nullable',
                    'global_tag_id' => 'nullable',
                    'type' =>  ['nullable'],
                    'attribute_id' => 'required_if:type,==,attribute',
                ];
            case 'PUT':
                return [
                    'name' => [
                        'required',
                        Rule::unique('standard_tags')->where(function ($query) use($name,$type) {
                            return $query->where('name', $name)->where('type', $type);
                        })->ignore(request()->input('id')),
                    ],
                    'parent_id' => 'nullable',
                    'global_tag_id' => 'nullable',
                    'type' =>  ['nullable'],
                    'attribute_id' => 'required_if:type,==,attribute',
                ];
            }
    }

    public function messages()
    { 
        return [
            'attribute_id.required_if' => "Attribute type field is required",
        ];
    }
}
