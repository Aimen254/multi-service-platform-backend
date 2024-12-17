<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewsPaperLogoRequest extends FormRequest
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
        $logo = config()->get('image.media.news_Paper_logo');
        $logoKeys = array_keys($logo);
        $width = $logo['width'];
        $height = $logo['height'];
        return [
            'value' => [
                'required',
                'mimes:jpeg,png,jpg',
                "dimensions:$logoKeys[0]=$width,$logoKeys[1]=$height"
            ],
        ];
    }

     /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        $logo = config()->get('image.media.news_Paper_logo');
        $width = $logo['width'];
        $height = $logo['height'];
        return [
            'value.mimes' => "Logo must be jpeg, png or jpg",
            'value.dimensions' => "Logo dimensions must be of $width x $height"
        ];
    }
}
