<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class NewsCategoryRequest extends FormRequest
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
                    'category_name' => ['required', Rule::unique('news_categories','category_name')],
                ];
            case 'PUT':
                $id = $this->route('category');
                return [
                    'category_name' => ['required', Rule::unique('news_categories','category_name')->ignore($id)],
                ];
        }
    }
}
