<?php

namespace Modules\Services\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $image = config()->get('services-module.media.services');
        $imageKeys = array_keys($image);
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];
        switch (request()->getMethod()) {
            case 'POST':
                return [
                    'image' => [
                        'required',
                        'mimes:jpeg,png,jpg',
                        "max:$size",
                        "dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height"
                    ],
                    'name' => ['required'],
                    'price' => ['required', 'numeric'],
                    'level_two_tag' => 'required',
                    'level_three_tag' => 'required',
                    'level_four_tags' => 'required',
                ];
            case 'PUT':
                switch (request()->type) {
                        // for product basic information
                    case 'basic_information':
                        return [
                            'name' => ['required'],
                            'price' => ['required', 'numeric'],
                            'level_two_tag' => 'required',
                            'level_three_tag' => 'required',
                            'level_four_tags' => ['required'],
                            'description' => 'nullable|max:20000',
                        ];
                        break;
                }
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
        $image = config()->get('services-module.media.services');
        $imageKeys = array_keys($image);
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];
        return [
            'image.max' => "Image size must be $size kb",
            'image.dimensions' => "Image dimensions must be of $width x $height."
        ];
    }
}
