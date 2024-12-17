<?php

namespace Modules\Obituaries\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Obituaries\Rules\DateOfBirth;
use Illuminate\Foundation\Http\FormRequest;

class ObituariesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // obituaries image
        $image = \config()->get('obituaries.media.obituaries');
        $imageKeys = array_keys($image);
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];

        // obituaries description length
        $descriptionLength = \config()->get('obituaries.description.length');

        switch ($this->getMethod()) {
            case 'POST':
                return [
                    'image' => [
                        'required',
                        'mimes:jpeg,png,jpg',
                        "max:$size",
                        "dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height"
                    ],
                    'name' => ['required', 'string', 'max:255'],
                    'date_of_birth' => ['required', new DateOfBirth],
                    'date_of_death' => ['required'],
                    'level_two_tag' => 'required|not_in:default',
                    'level_three_tag' => 'required|not_in:default',
                    'level_four_tags' => 'required|not_in:default',
                    'description' => ["nullable", "max:$descriptionLength"],
                    'is_commentable' => ['required', 'boolean'],
                    'is_featured' => ['nullable', 'boolean'],

                ];
            case 'PUT':
                return [
                    'image' => [
                        'nullable',
                        'mimes:jpeg,png,jpg',
                        "max:$size",
                        "dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height"
                    ],
                    'name' => ['required', 'string', 'max:255'],
                    'date_of_birth' => ['required', new DateOfBirth],
                    'date_of_death' => ['required'],
                    'level_two_tag' => 'required|not_in:default',
                    'level_three_tag' => 'required|not_in:default',
                    'level_four_tags' => 'required|not_in:default',
                    'description' => ["nullable", "max:$descriptionLength"],
                    'is_commentable' => ['required', 'boolean'],
                    'is_featured' => ['required', 'boolean']
                ];
        }
    }

    public function messages()
    {
        return [
            'image.max' => "Image size must be :max kb",
            'image.dimensions' => "Image dimensions must be of :width x :height.",
            'name.required' => "The title field is required.",
            'name.max' => "The title field must not be greater than :max numbers.",
            'is_commentable.required' => "The commentable field is required."
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
