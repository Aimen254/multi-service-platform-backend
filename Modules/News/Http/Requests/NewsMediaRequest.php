<?php

namespace Modules\News\Http\Requests;

use App\Rules\CheckProductMediaCount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;

class NewsMediaRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $image = \config()->get('news.media.news');
        $imageKeys = array_keys($image);
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];

        return [
            'file.*' => [
                "mimes:jpeg,png,jpg",
                "max:$size",
                "dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height",
                new CheckProductMediaCount('product')
            ]
        ];
    }

    public function messages()
    {
        return [
            'file.*.mimes' => 'Only jpeg, png, jpg files can be uploaded!',
            'file.*.max' => 'Maximum file size should be :max kbs',
            'file.*.dimensions' => 'Image has invalid dimensions',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => $validator->errors()->first()
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
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
