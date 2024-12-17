<?php

namespace App\Http\Requests\API;

use App\Models\StandardTag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductMainImageRequest extends FormRequest
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
     *
     * @return array
     */
    public function rules()
    {
        $config = $this->fileConfigurations();

        return [
            'image' => [
                'mimes:jpeg,png,jpg',
                "max:{$config['size']}",
                "dimensions:width={$config['width']},height={$config['height']}",
            ]
        ];
    }

    protected function getModule(): string
    {
        return StandardTag::where('id', $this->route('module_id'))->orWhere('slug', $this->route('module_id'))->first()->slug;
    }

    protected function fileConfigurations(): array
    {
        $module = $this->getModule();
        switch ($module) {
            case 'automotive':
                $image = \config()->get('automotive.media.vehicle');
                break;
            case 'boats':
                $image = \config()->get('boats.media.boat');
                break;
            case 'taskers':
                $image = \config()->get('taskers.media.tasker');
                break;
            case 'news':
                $image = \config()->get('news.media.news');
                break;
            case 'recipes':
                $image = \config()->get('recipes.media.recipes');
                break;
            case 'blogs':
                $image = \config()->get('blogs.media.blog');
                break;
            case 'obituaries':
                $image = \config()->get('obituaries.media.obituaries');
                break;
            case 'services':
                $image = \config()->get('services-module.media.services');
                break;
            case 'employment':
                $image = \config()->get('employment.media.posts');
                break;
            case 'notices':
                $image = \config()->get('notices.media.notice');
                break;
            case 'government':
                $image = \config()->get('government.media.posts');
                break;
            case 'real-estate':
                    $image = \config()->get('realestate.media.property');
                    break;
            case 'events':
                    $image = \config()->get('events.media.events');
                     break;
            default:
                $image = \config()->get('classifieds.media.classified');
                break;
        }

        $imageKeys = array_keys($image);
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];

        return [
            'keys' => $imageKeys,
            'width' => $width,
            'height' => $height,
            'size' => $size
        ];
    }

    public function messages()
    {
        return [
            'image.dimensions' => 'The image has invalid dimensions.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            'message' => $validator->errors()->first()
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY,);
        throw new ValidationException($validator, $response);
    }
}
