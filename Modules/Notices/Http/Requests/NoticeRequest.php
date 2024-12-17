<?php

namespace Modules\Notices\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NoticeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $image = config()->get('notices.media.notice');
        $imageKeys = array_keys($image);
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];
        switch (request()->getMethod()) {
            case 'POST':
                return [
                    'image' => [
                        'sometimes',
                        'mimes:jpeg,png,jpg',
                        "max:$size",
                        "dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height"
                    ],
                    'type' => 'required',
                    'level_two_tag' => 'required',
                    'level_three_tag' => 'required',
                    'level_four_tags' => 'required',
                    'description' => 'required|max:20000',
                ];
            case 'PUT':
                // for product basic information
                return [
                    'type' => 'required',
                    'level_two_tag' => 'required',
                    'level_three_tag' => 'required',
                    'level_four_tags' => ['required'],
                    'description' => 'required|max:20000',
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
        return \auth()->check();
    }

    public function messages()
    {
        $image = config()->get('notices.media.notice');
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];
        return [
            'image.max' => "Image size must be $size kb",
            'image.dimensions' => "Image dimensions must be of $width x $height."
        ];
    }
}
