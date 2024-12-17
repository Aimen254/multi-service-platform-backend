<?php

namespace App\Http\Requests\API;

use App\Models\StandardTag;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;

class PublicProfileRequest extends FormRequest
{
    protected $imageConfig;
    protected $coverImageConfig;

    public function __construct()
    {
        $this->imageConfig = config()->get('image.media.public_profile.avatar');
        $this->coverImageConfig = config()->get('image.media.public_profile.banner');
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => [
                'nullable',
                'in:avatar,cover_image',
                Rule::requiredIf(function () {
                    return request()->module !== 'taskers' && empty(request()->input('name')) && empty(request()->input('description'));
                }),
            ],
            'image' => ["nullable", "dimensions:width={$this->imageConfig['width']},height={$this->imageConfig['height']}", "max:{$this->imageConfig['size']}", "mimes:png,jpg,jpeg", 
                Rule::requiredIf(function() {
                    return (request()->input('module') == 'posts') || request()->input('type') == 'avatar';
                })
            ],
            'cover_image' => ["nullable", "dimensions:width={$this->coverImageConfig['width']},height={$this->coverImageConfig['height']}", "max:{$this->coverImageConfig['size']}", "mimes:png,jpg,jpeg", 
                Rule::requiredIf(function() {
                    return (request()->input('module') == 'posts' && !request()->input('id')) || request()->input('type') == 'cover_image';
                })
            ],
            'module_id' => ['required', 'max:20'],
            'name' => [
                'nullable', 'string', 'max:255',
                Rule::requiredIf(function () {
                    return (request()->module !== 'taskers' && !request()->cover_image && !request()->image) || request()->module == 'posts';
                }),
            ],
            'nick_name' => [
               'nullable', 'string', 'max:255',
                Rule::requiredIf(function () {
                    return request()->module == 'posts';
                }),
                Rule::unique('public_profiles')
                ->ignore(request()->input('id')), // if you're updating the profile
                'regex:/^@?[a-zA-Z0-9_]+$/',
            ],
            'description' => [
                'nullable', 'string', 'max:10000',
                Rule::requiredIf(function () {
                    return request()->module == 'taskers' && !request()->cover_image && !request()->image;
                }),
            ],
            'is_name_visible' => ['nullable'],
            'level_two_tags' => [
                'nullable',
                Rule::requiredIf(function () {
                    return request()->module == 'taskers' && !request()->cover_image && !request()->image;
                }),
            ],
            'is_public' => ['nullable'],
            'level_three_tags' => [
                'nullable',
                Rule::requiredIf(function () {
                    return request()->module == 'taskers' && !request()->cover_image && !request()->image;
                }),
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "image.dimensions" => "Avatar dimensions must be of {$this->imageConfig['width']} x {$this->imageConfig['height']}",
            "cover_image.dimensions" => "Cover image dimensions must be of {$this->coverImageConfig['width']} x {$this->coverImageConfig['height']}",
            "name.required_without_all" => "The :attribute field is required.",
            "image.required" => "The avatar field is required.",
            "nick_name.required" => "The user id field is required.",
            'nick_name.regex' => 'The user id may only contain letters, numbers, and _.',
            'nick_name.unique' => 'The user id has already been taken.'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            'message' => $validator->errors(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY,);

        throw new ValidationException($validator, $response);
    }

    protected function prepareForValidation()
    {
        if (request()->input('module')) {
            $module = StandardTag::where('id', request()->input('module'))
                ->orWhere('slug', request()->input('module'))->first();
            $this->merge(['module_id' => $module?->id]);
        }
        if (request()->input('level_two_tags')) {
            $this->merge(['level_two_tags' => json_decode(request()->input('level_two_tags'))]);
        }
        if (request()->input('level_three_tags')) {
            $this->merge(['level_three_tags' => json_decode(request()->input('level_three_tags'))]);
        }

        if(request()->input('is_public')) {
            $this->merge(['is_public' => filter_var(request()->input('is_public'), FILTER_VALIDATE_BOOLEAN)]);
        }
    }
}
