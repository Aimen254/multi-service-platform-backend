<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttributeTagRequest extends FormRequest
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
                    'name' => 'required',
                    'parent_id' => 'nullable',
                    'global_tag_id' => 'required'
                    // 'type' =>  ['required', 'in:product,variant,brand'],
                ];
            case 'PUT':
                return [
                    'name' => 'required',
                    'parent_id' => 'nullable',
                    'global_tag_id' => 'required'
                    // 'type' =>  ['required', 'in:product,variant,brand'],
                ];
            }
    }
}
