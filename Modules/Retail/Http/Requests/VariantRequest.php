<?php

namespace Modules\Retail\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $image = config()->get('retail.media.product');
        $imageKeys = array_keys($image);
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];
        switch (request()->getMethod()) {
            case 'POST':
                return [
                    'title' => ['required'],
                    'price' => ['required', 'numeric'],
                    'quantity' => ['min:0'],
                    'image' => [
                        'required',
                        'mimes:jpeg,png,jpg',
                        "max:$size",
                        "dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height"
                    ],
                ];
                break;
            case 'PUT':
                return [
                    'title' => ['required'],
                    'price' => ['required', 'numeric'],
                    'quantity' => ['min:0'],
                    'image' => [
                        'nullable',
                        'mimes:jpeg,png,jpg',
                        "max:$size",
                        "dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height"
                    ],
                ];
        }
    }
}
