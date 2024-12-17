<?php

namespace Modules\Boats\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Boats\Rules\BoatStockRule;

class BoatRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $image = config()->get('boats.media.boat');
        $imageKeys = array_keys($image);
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];
        switch (request()->getMethod()) {
            case 'POST':
                return [
                    'image' => [
                        'nullable',
                        'mimes:jpeg,png,jpg',
                        "max:$size",
                        "dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height"
                    ],
                    'type' => ['required'],
                    'year' => ['required'],
                    'price' => ['nullable', 'numeric'],
                    'level_two_tag' => 'required',
                    'level_three_tag' => 'required',
                    'level_four_tags' => 'required',
                ];
            case 'PUT':
                // for vehicle basic information
                return [
                    'type' => ['required'],
                    'year' => ['required'],
                    'price' => ['nullable', 'numeric'],
                    'level_two_tag' => 'required',
                    'level_three_tag' => 'required',
                    'level_four_tags' => 'required',
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
        $avatar = config()->get('boats.media.boat');
        $width = $avatar['width'];
        $height = $avatar['height'];
        $size = $avatar['size'];
        return [
            'image.max' => "Image size must be $size kb",
            'image.dimensions' => "Image dimensions must be of $width x $height."
        ];
    }
}
