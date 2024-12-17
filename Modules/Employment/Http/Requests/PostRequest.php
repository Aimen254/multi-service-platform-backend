<?php

namespace Modules\Employment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $image = config()->get('employment.media.posts');
        $imageKeys = array_keys($image);
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];
        $descriptionLength = \config()->get('employment.description.length');

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
                    'description' => ["nullable", "max:$descriptionLength"],
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
                            'description' => ["nullable", "max:$descriptionLength"],
                        ];
                        break;
                }
        }
    }

    public function messages()
    {
        return [
            'image.max' => "Image size must be :max kb",
            'image.dimensions' => "Image dimensions must be of :width x :height.",
            'name.required' => "The title field is required.",
            'price.required' => "The Salary field is required.",
            'name.max' => "The name field must not be greater than :max numbers.",
        ];
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
}
