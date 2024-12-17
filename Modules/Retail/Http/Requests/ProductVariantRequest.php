<?php

namespace Modules\Retail\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;


class ProductVariantRequest extends FormRequest
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

        $rules = [
            'title' => ['required'],
            'price' => ['required', 'numeric', 'min:0'],
            'color' => ['nullable', 'exists:colors,id'],
            'size'  => ['nullable', 'exists:sizes,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
        ];
        switch (request()->getMethod()) {
            case 'POST': // Create request
                $rules['image'] = [
                    'required',
                    'mimes:jpeg,png,jpg',
                    "max:$size",
                    "dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height"
                ];
                break;
            case 'PUT':
                $rules['image'] = [
                    'nullable',
                    'mimes:jpeg,png,jpg',
                    "max:$size",
                    "dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height"
                ];
                break;
        }

        return $rules;
    }


    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $color = $this->input('color');
            $size = $this->input('size');

            if (is_null($color) && is_null($size)) {
                $validator->errors()->add('color', 'At least one of color or size must be selected.');
                $validator->errors()->add('size', 'At least one of color or size must be selected.');
            }
        });
    }

    public function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            'message' => $validator->errors(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY,);
        throw new ValidationException($validator, $response);
        // use default behavior for web.php routes
        parent::failedValidation($validator);
    }
}
