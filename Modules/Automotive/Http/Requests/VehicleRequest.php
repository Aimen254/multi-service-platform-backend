<?php

namespace Modules\Automotive\Http\Requests;

use App\Models\StandardTag;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Automotive\Rules\VehicleStockRule;

class VehicleRequest extends FormRequest
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
        $image = \config()->get('automotive.media.vehicle');
        $moduleId = request()->route('moduleId');
        $module = StandardTag::where('id', $moduleId)->orWhere('slug', $moduleId)->first()->slug;
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
                    'type' => ['required'],
                    'year' => ['required'],
                    'mileage' => ['nullable', Rule::requiredIf($module === 'automotive'), 'numeric'],
                    'vin' => ['nullable', Rule::requiredIf($module === 'automotive')],
                    'price' => ['nullable', Rule::requiredIf($module === 'automotive'), 'numeric'],
                    'hierarchies.0.level_two_tag'=> 'required|not_in:default',
                    'hierarchies.0.level_three_tag'=> 'required|not_in:default',
                    'hierarchies.0.level_four_tags'=> 'required|not_in:default'
                ];
            case 'PUT':
                // for vehicle basic information
                return [
                    'image' => [
                        'nullable',
                        'mimes:jpeg,png,jpg',
                        "max:$size",
                        "dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height"
                    ],
                    'name' => ['required'],
                    'type' => ['required'],
                    'year' => ['required'],
                    'trim' => 'nullable',
                    'is_featured' => ['required', 'boolean'],
                    'mileage' => ['nullable', Rule::requiredIf($module === 'automotive'), 'numeric'],
                    'vin' => ['nullable', Rule::requiredIf($module === 'automotive')],
                    'price' => ['nullable', Rule::requiredIf($module === 'automotive'), 'numeric'],
                    'hierarchies.0.level_two_tag'=> 'required|not_in:default',
                    'hierarchies.0.level_three_tag'=> 'required|not_in:default',
                    'hierarchies.0.level_four_tags'=> 'required|not_in:default'
                ];
        }
    }
    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        $avatar = \config()->get('automotive.media.vehicle');
        $width = $avatar['width'];
        $height = $avatar['height'];
        $size = $avatar['size'];
        return [
            'image.max' => "Image size must be $size kb",
            'image.dimensions' => "Image dimensions must be of $width x $height.",
            'hierarchies.0.level_two_tag.required' => 'Level two tag is required',
            'hierarchies.0.level_three_tag.required' => 'Level three tag is required',
            'hierarchies.0.level_four_tags.required' => 'Level four tag is required',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $routeName = $this->route()->getName();
        if (strpos($routeName, 'products.store') !== false || strpos($routeName, 'products.update') !== false) {
            $response = new JsonResponse([
                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY,);
            throw new ValidationException($validator, $response);
        }
        // use default behavior for web.php routes
        parent::failedValidation($validator);
    }
}
