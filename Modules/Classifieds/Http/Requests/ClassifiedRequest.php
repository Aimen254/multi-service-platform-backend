<?php

namespace Modules\Classifieds\Http\Requests;

use Modules\Retail\Rules\ProductStockRule;
use Illuminate\Foundation\Http\FormRequest;

class ClassifiedRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // classified image
        $image = \config()->get('classifieds.media.classified');
        $imageKeys = array_keys($image);
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];

        // classified description length
        $descriptionLength = \config()->get('classifieds.description.length');

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
                    'price' => ['required', 'numeric'],
                    'weight' => ['nullable', 'numeric', 'min:0'],
                    'weight_unit' => ['nullable', 'in:kg,pound'],
                    'stock' => ['required', new ProductStockRule()],
                    'level_two_tag' => 'required|not_in:default',
                    'level_three_tag' => 'required|not_in:default',
                    'level_four_tags' => 'required|not_in:default',
                    'description' => ["nullable", "max:$descriptionLength"],
                    'is_featured' => ['required', 'boolean'],
                    'is_deliverable' => ['required', 'boolean'],
                    'is_commentable' => ['required', 'boolean'],
                    'cryptocurrency_accepted' => ['required', 'boolean']
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
                    'price' => ['required', 'numeric'],
                    'weight' => ['nullable', 'numeric', 'min:0'],
                    'weight_unit' => ['nullable', 'in:kg,pound'],
                    'stock' => ['required', new ProductStockRule()],
                    'level_two_tag' => 'required|not_in:default',
                    'level_three_tag' => 'required|not_in:default',
                    'level_four_tags' => 'required|not_in:default',
                    'description' => ["nullable", "max:$descriptionLength"],
                    'package_count' => ['nullable', 'numeric'],
                    'is_featured' => ['required', 'boolean'],
                    'is_deliverable' => ['required', 'boolean'],
                    'is_commentable' => ['required', 'boolean'],
                    'cryptocurrency_accepted' => ['required', 'boolean']
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
        return auth()->check();
    }
}
