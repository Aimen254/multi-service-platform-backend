<?php

namespace Modules\News\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // news image
        $image = \config()->get('news.media.news');
        $imageKeys = array_keys($image);
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];

        // news description length
        $descriptionLength = \config()->get('news.description.length');

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
                    'level_two_tag' => 'required|not_in:default',
                    'level_three_tag' => 'required|not_in:default',
                    'level_four_tags' => 'required|not_in:default',
                    'description' => ["nullable", "max:$descriptionLength"],
                    'is_featured' => ['required', 'boolean'],
                    'is_commentable' => ['required', 'boolean']
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
                    'level_two_tag' => 'required|not_in:default',
                    'level_three_tag' => 'required|not_in:default',
                    'level_four_tags' => 'required|not_in:default',
                    'description' => ["nullable", "max:$descriptionLength"],
                    'is_featured' => ['required', 'boolean'],
                    'is_commentable' => ['required', 'boolean']
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
            'is_featured.required' => "The featured field is required.",
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