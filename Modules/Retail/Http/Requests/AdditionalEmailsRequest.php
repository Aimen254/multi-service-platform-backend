<?php

namespace Modules\Retail\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdditionalEmailsRequest extends FormRequest
{
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
                    'personal_name' => 'required',
                    'email' => ['required', 'email', 'unique:business_additional_emails'],
                    'title' => 'required',
                ];
            case 'PUT':
                $id = request()->id;
                return [
                    'personal_name' => 'required',
                    'email' => ['required', 'email', 'unique:business_additional_emails,email,' . $id],
                    'title' => 'required',
                ];
        }
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
}
