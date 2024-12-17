<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttributeRequest extends FormRequest
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
                    'name' => 'required| unique:attributes',
                    'module' => 'required',
                    'manual_position' => 'nullable'
                ];
            case 'PUT':
                return [
                    'name' => 'required| unique:attributes,name,' . request()->id,
                    'module' => 'required',
                    'manual_position' => 'nullable'
                ];
            }
    }
}
