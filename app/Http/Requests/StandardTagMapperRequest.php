<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StandardTagMapperRequest extends FormRequest
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
            'standardTag' =>  ['nullable', Rule::requiredIf(fn () => request()->type == 'tag')],
            'attribute' => ['nullable', Rule::requiredIf(fn () => request()->type == 'attribute')],
            'brand' => ['nullable', Rule::requiredIf(fn () => request()->type == 'brand')],
        ];
    }
}
