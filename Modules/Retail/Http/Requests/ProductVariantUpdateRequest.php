<?php

namespace Modules\Retail\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ProductVariantUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $image = config()->get('retail.media.product');
        $imageKeys = array_keys($image);
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];

        return [
            'title' => ['required'],
            'price' => ['required', 'numeric', 'min:0'],
            'color' => ['nullable', 'exists:colors,id'],
            'size'  => ['nullable', 'exists:sizes,id'],
            'quantity' => ['nullable','numeric','min:0'],
            'image' => [
                'nullable',
                'mimes:jpeg,png,jpg',
                "max:$size",
                "dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height"
            ],
        ];
    }
}
