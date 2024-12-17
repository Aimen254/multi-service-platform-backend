<?php

namespace Modules\Automotive\Http\Requests;

use App\Rules\MediaRule;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class GarageRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $logo = config()->get('image.media.logo');
        $logoKeys = array_keys($logo);
        $logoWidth = $logo['width'];
        $logoHeight = $logo['height'];
        $logoSize = $logo['size'];

        $thumbnail = config()->get('image.media.thumbnail');
        $thumbnailKeys = array_keys($thumbnail);
        $thumbnailWidth = $thumbnail['width'];
        $thumbnailHeight = $thumbnail['height'];
        $thumbnailSize = $thumbnail['size'];

        $banner = config()->get('image.media.banner');
        $bannerKeys = array_keys($banner);
        $bannerWidth = $banner['width'];
        $bannerHeight = $banner['height'];
        $bannerSize = $banner['size'];

        $secondaryBanner = config()->get('image.media.secondaryBanner');
        $secondaryBannerKeys = array_keys($secondaryBanner);
        $secondaryBannerWidth = $secondaryBanner['width'];
        $secondaryBannerHeight = $secondaryBanner['height'];
        $secondaryBannerSize = $secondaryBanner['size'];

        switch (request()->getMethod()) {
            case 'POST':
                $isRequired = request()->input('isRequired', false);
                $module = $this->segment(3);
                return [
                    'logo' => [
                        Rule::requiredIf($module === 'retail'),
                        'mimes:png,jpg,jpeg',
                        "max:$logoSize",
                        "dimensions:$logoKeys[0]=$logoWidth,$logoKeys[1]=$logoHeight"
                    ],

                    'banner' => [
                        Rule::requiredIf($module === 'retail'),
                        'mimes:png,jpg,jpeg',
                        "max:$bannerSize",
                        "dimensions:$bannerKeys[0]=$bannerWidth,$bannerKeys[1]=$bannerHeight"
                    ],
                    'secondaryBanner' => [
                        Rule::requiredIf($module === 'retail'),
                        'mimes:png,jpg,jpeg',
                        "max:$secondaryBannerSize",
                        "dimensions:width=$secondaryBannerWidth,height=$secondaryBannerHeight"
                    ],
                    'name' => Rule::requiredIf(!$isRequired),
                    'user_name' => ['nullable', 'alpha_num', 'unique:businesses,user_name'],
                    'slug' => Rule::requiredIf(!$isRequired),
                    'email' => Rule::requiredIf(!$isRequired),
                    'owner_id' => ['nullable', 'exists:users,id'],
                    'address' => Rule::requiredIf(function () use ($isRequired) {
                        return !$isRequired && request()->user()->hasRole(['business_owner', 'admin', 'newspaper']);
                    }),
                    'city' => Rule::requiredIf(function () use ($isRequired) {
                        return !$isRequired && request()->user()->hasRole(['customer']);
                    }),
                    'home_delivery' => 'nullable',
                    'virtual_appointments' => 'nullable',
                    'phone' => [
                        Rule::requiredIf(function () use ($isRequired) {
                            return !$isRequired && request()->user()->hasRole(['business_owner', 'admin', 'newspaper']);
                        }),
                        Rule::when(function () use ($isRequired) {
                            return !$isRequired && request()->user()->hasRole(['business_owner', 'admin', 'newspaper']);
                        }, ['regex:/^\+\d{1,3}\d{9,}$/'])
                    ],
                    'status' => 'nullable',
                    'long_description' => ['nullable', 'max:20000', new MediaRule],
                    'message'=>['nullable','max:500']
                ];

            case 'PUT':
                $isRequired = request()->input('isRequired', false);
                $businessId = request()->input('id');
                $module = 'retail'; // Assuming module is passed in the request

                return [
                    'name' => Rule::requiredIf(!$isRequired),
                    'user_name' => ['nullable', 'alpha_num', 'without_spaces', 'unique:businesses,user_name,' . $businessId],
                    'slug' => Rule::requiredIf(!$isRequired),
                    'email' => Rule::requiredIf(!$isRequired),
                    'is_featured' => ['nullable', Rule::requiredIf(function () {
                        return !request()->input('front_end_flag');
                    }), 'boolean'],
                    'owner_id' => ['nullable', 'exists:users,id'],
                    'address' => Rule::requiredIf(function () use ($isRequired) {
                        return !$isRequired && request()->user()->hasRole(['business_owner', 'admin', 'newspaper']);
                    }),
                    'city' => Rule::requiredIf(function () use ($isRequired) {
                        return !$isRequired && request()->user()->hasRole(['customer']);
                    }),
                    'home_delivery' => 'nullable',
                    'virtual_appointments' => 'nullable',
                    'phone' => [
                        Rule::requiredIf(function () use ($isRequired) {
                            return !$isRequired && request()->user()->hasRole(['business_owner', 'admin', 'newspaper']);
                        }),
                        Rule::when(function () use ($isRequired) {
                            return !$isRequired && request()->user()->hasRole(['business_owner', 'admin', 'newspaper']);
                        }, ['regex:/^\+\d{1,3}\d{9,}$/'])
                    ],
                    'logo' => [
                        'sometimes',
                        'mimes:png,jpg,jpeg',
                        "max:$logoSize",
                        "dimensions:$logoKeys[0]=$logoWidth,$logoKeys[1]=$logoHeight"
                    ],
                    'thumbnail' => [
                        'sometimes',
                        'mimes:png,jpg,jpeg',
                        "max:$thumbnailSize",
                        "dimensions:$thumbnailKeys[0]=$thumbnailWidth,$thumbnailKeys[1]=$thumbnailHeight"
                    ],
                    'banner' => [
                        'sometimes',
                        'mimes:png,jpg,jpeg',
                        "max:$bannerSize",
                        "dimensions:$bannerKeys[0]=$bannerWidth,$bannerKeys[1]=$bannerHeight"
                    ],
                    'secondaryBanner' => [
                        'sometimes',
                        'mimes:png,jpg,jpeg',
                        "max:$secondaryBannerSize",
                        "dimensions:width=$secondaryBannerWidth,height=$secondaryBannerHeight"
                    ],
                    'long_description' => ['nullable', 'max:20000', new MediaRule],
                    'message'=>['nullable','max:500']
                ];
        }

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

    public function prepareForValidation()
    {
        $this->merge([
            'owner_id' => $this->input('owner_id') ?? $this->user()->id,
        ]);
    }

    public function attributes()
    {
        return [

            'phone' => 'phone number',

        ];
    }


    public function messages()
    {
        $logoSize = config()->get('image.media.logo.size');
        $thumbnailSize = config()->get('image.media.thumbnail.size');
        $bannerSize = config()->get('image.media.banner.size');
        $secondaryBannerSize = config()->get('image.media.secondaryBanner.size');
        return [
            'logo.dimensions' => "The logo has invalid dimensions.",
            'thumbnail.dimensions' => "The thumbnail has invalid dimensions.",
            'banner.dimensions' => "The banner has invalid dimensions.",
            'secondaryBanner.dimensions' => "The secondary banner has invalid dimensions.",
            'logo.max' => "The logo must not be greater than " . $logoSize . " KB.",
            'thumbnail.max' => "The thumbnail must not be greater than " . $thumbnailSize . " KB.",
            'banner.max' => "The banner must not be greater than " . $bannerSize . " KB.",
            'secondaryBanner.max' => "The secondary banner must not be greater than " . $secondaryBannerSize . " KB.",
        ];
    }
    public function failedValidation(Validator $validator)
    {
        $routeName = $this->route()->getName();
        if (strpos($routeName, 'businesses.store') !== false || strpos($routeName, 'businesses.update') !== false) {
            $response = new JsonResponse([
                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY,);
            throw new ValidationException($validator, $response);
        }
        // use default behavior for web.php routes
        parent::failedValidation($validator);
    }
}
