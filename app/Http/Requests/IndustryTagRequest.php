<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndustryTagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $icon = config()->get('image.media.icon');
        $iconKeys = array_keys($icon);
        $width = $icon['width'];
        $height = $icon['height'];
        $size = $icon['size'];
        $type = request()->type;
        switch (request()->getMethod()) {
            case 'POST':
                return [
                    'name' => [
                        'required',
                        Rule::unique('standard_tags'),
                    ],
                    'icon' => [
                            'nullable',
                            'mimes:jpeg,png,jpg',
                            "max:$size",
                            "dimensions:$iconKeys[0]=$width,$iconKeys[1]=$height"
                    ],
                ];
            case 'PUT':
                return [
                    'name' => [
                        'required',
                        Rule::unique('standard_tags')->ignore(request()->id)
                    ],
                    'icon' => [
                        'nullable',
                        'mimes:jpeg,png,jpg',
                        "max:$size",
                        "dimensions:$iconKeys[0]=$width,$iconKeys[1]=$height"
                    ],
                ];
            }
    }
}
