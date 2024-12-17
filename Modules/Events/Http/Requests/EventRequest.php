<?php

namespace Modules\Events\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // blog image
        $image = \config()->get('events.media.events');
        $imageKeys = array_keys($image);
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];

        // blog description length
        $descriptionLength = \config()->get('blogs.description.length');

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
                    'performer' => ['required', 'string', 'max:255'],
                    'price' => ['required', 'integer', 'not_in:0'],
                    'max_price' => ['required', 'integer', 'gt:price', 'not_in:0'],
                    'level_two_tag' => 'required|not_in:default',
                    'level_three_tag' => 'required|not_in:default',
                    'level_four_tags' => 'required|not_in:default',
                    'description' => ["nullable", "max:$descriptionLength"],
                    'is_featured' => ['required', 'boolean'],
                    'is_commentable' => ['required', 'boolean'],
                    'ticket_url' => ['required', 'url', 'max:255'],
                    'away_team' => ['nullable', 'string', 'max:255'],
                    'event_date' => ['required'],
                    'event_location' => ['required', 'string', 'max:255'],
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
                    'event_date' => ['required'],
                    'performer' => ['required', 'string', 'max:255'],
                    'price' => ['required', 'integer', 'not_in:0'],
                    'max_price' => ['required', 'integer', 'gt:price', 'not_in:0'],
                    'event_location' => ['required', 'string', 'max:255'],
                    'level_two_tag' => 'required|not_in:default',
                    'level_three_tag' => 'required|not_in:default',
                    'level_four_tags' => 'required|not_in:default',
                    'description' => ["nullable", "max:$descriptionLength"],
                    'is_featured' => ['required', 'boolean'],
                    'is_commentable' => ['required', 'boolean'],
                    'ticket_url' => ['required', 'url', 'max:255'],
                    'away_team' => ['nullable', 'string', 'max:255'],

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
            'is_commentable.required' => "The commentable field is required.",
            'price.required' => "The minimum price field is required.",
            'max_price.required' => "The maximum price field is required.",
            'event_date.required' => "The event date and time field is required."
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





/**
 * Determine if the user is authorized to make this request.
 *
 * @return bool
 */
