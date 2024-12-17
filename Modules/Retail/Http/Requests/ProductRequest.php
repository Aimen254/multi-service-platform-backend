<?php

namespace Modules\Retail\Http\Requests;

use Modules\Retail\Rules\ProductStockRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
                    'image' => [
                        'required',
                        'mimes:jpeg,png,jpg',
                        "max:$size",
                        "dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height"
                    ],
                    'name' => ['required'],
                    'price' => ['required', 'numeric'],
                    'weight' => ['nullable', 'numeric', 'min:0'],
                    'level_two_tag' => 'required',
                    'level_three_tag' => 'required',
                    'level_four_tags' => 'required',
                    'stock' => ['required', new ProductStockRule()]
                ];
            case 'PUT':
                switch (request()->type) {
                        // for product basic information
                    case 'basic_information':
                        return [
                            'name' => ['required'],
                            'price' => ['required', 'numeric'],
                            'weight' => ['nullable', 'numeric', 'min:0'],
                            'level_two_tag' => 'required',
                            'level_three_tag' => 'required',
                            'level_four_tags' => ['required'],
                            'stock' => ['required', new ProductStockRule()]
                        ];
                        break;
                }
        }
    }
    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        $avatar = config()->get('retail.media.product');
        $width = $avatar['width'];
        $height = $avatar['height'];
        $size = $avatar['size'];
        return [
            'image.max' => "Image size must be $size kb",
            'image.dimensions' => "Image dimensions must be of $width x $height."
        ];
    }
}
