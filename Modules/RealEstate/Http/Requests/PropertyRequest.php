<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $image = config()->get('realestate.media.property');
        $imageKeys = array_keys($image);
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];
        $descriptionLength = \config()->get('realestate.description.length');

        switch (request()->getMethod()) {
            case 'POST':
                return [
                    'image' => [
                        'required',
                        'mimes:jpeg,png,jpg',
                        "max:$size",
                        "dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height"
                    ],
                    'price' => 'required',
                    'name' => ['required'],
                    'level_two_tag' => 'required',
                    'level_three_tag' => 'required',
                    'level_four_tags' => 'required',
                    'description' => ["nullable", "max:$descriptionLength"],
                    'user_id' => ['required'],
                    'type' => ['required', 'string', 'max:100'],
                ];
            case 'PUT':
                // for product basic information
                return [
                    'name' => ['required'],
                    'level_two_tag' => 'required',
                    'level_three_tag' => 'required',
                    'level_four_tags' => ['required'],
                    'description' => ["nullable", "max:$descriptionLength"],
                    'user_id' => ['required'],
                    'price' => 'required',
                    'type' => ['required', 'string', 'max:100'],
                ];
                break;
        }
    }

    public function messages()
    {
        $image = config()->get('realestate.media.property');
        $width = $image['width'];
        $height = $image['height'];
        return [
            'image.max' => "Image size must be :max kb",
            'image.dimensions' => "Image dimensions must be of $width x $height.",
            'name.required' => "The title field is required.",
            'name.max' => "The name field must not be greater than :max numbers.",
        ];
    }

    public function attributes()
    {
        return [
            'user_id' => 'agent'
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
}
