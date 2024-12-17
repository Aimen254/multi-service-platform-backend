<?php

namespace Modules\Services\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialLinksRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'facebook_id' => 'nullable|url',
            'instagram_id' => 'nullable|url',
            'twitter_id' => 'nullable|url',
            'pinterest_id' => 'nullable|url'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'facebook_id.url' => 'Facbook id should be a link',
            'instagram_id.url' => 'Instagram id should be a link',
            'twitter_id.url' => 'Twitter id should be a link',
            'pinterest_id.url' => 'Pinterest id should be a link'
        ];
    }
}
