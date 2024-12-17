<?php

namespace Modules\Services\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class BusinessRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $logo = config()->get('services-module.media.logo');
        $logoKeys = array_keys($logo);
        $logoWidth = $logo['width'];
        $logoHeight = $logo['height'];
        $logoSize = $logo['size'];

        $thumbnail = config()->get('services-module.media.thumbnail');
        $thumbnailKeys = array_keys($thumbnail);
        $thumbnailWidth = $thumbnail['width'];
        $thumbnailHeight = $thumbnail['height'];
        $thumbnailSize = $thumbnail['size'];

        $banner = config()->get('services-module.media.banner');
        $bannerKeys = array_keys($banner);
        $bannerWidth = $banner['width'];
        $bannerHeight = $banner['height'];
        $bannerSize = $banner['size'];

        switch (request()->getMethod()) {
            case 'POST':
                return [
                    'name' => 'required',
                    'slug' => ['required', 'without_spaces'],
                    'email' => ['required'],
                    'owner_id' => ['nullable', 'exists:users,id'],
                    'address' => Rule::requiredIf(function () {
                        return request()->user()->hasRole(['business_owner', 'admin', 'newspaper']);
                    }),
                    'home_delivery' => 'nullable',
                    'virtual_appointments' => 'nullable',
                    'phone' => Rule::requiredIf(function () {
                        return request()->user()->hasRole(['business_owner', 'admin', 'newspaper']);
                    }),
                    'status' =>  'nullable',
                    'logo' => "sometimes|mimes:png,jpg,jpeg|max:$logoSize|dimensions:$logoKeys[0]=$logoWidth,$logoKeys[1]=$logoHeight",
                    'thumbnail' => "sometimes|mimes:png,jpg,jpeg|max:$thumbnailSize|dimensions:$thumbnailKeys[0]=$thumbnailWidth,$thumbnailKeys[1]=$thumbnailHeight",
                    'banner' => "sometimes|mimes:png,jpg,jpeg|max:$bannerSize|dimensions:$bannerKeys[0]=$bannerWidth,$bannerKeys[1]=$bannerHeight",
                ];
            case 'PUT':
                $businessId = request()->input('id');
                return [
                    'name' => 'required',
                    'slug' => ['required', 'without_spaces', 'unique:businesses,slug,' . $businessId],
                    'email' => ['required'],
                    'owner_id' => ['nullable', 'exists:users,id'],
                    'address' => Rule::requiredIf(function () {
                        return request()->user()->hasRole(['business_owner', 'admin', 'newspaper']);
                    }),
                    'city' => Rule::requiredIf(function () {
                        return request()->user()->hasRole(['customer']);
                    }),
                    'home_delivery' => 'nullable',
                    'virtual_appointments' => 'nullable',
                    'phone' => Rule::requiredIf(function () {
                        return request()->user()->hasRole(['business_owner', 'admin', 'newspaper']);
                    }),
                    'logo' => "sometimes|mimes:png,jpg,jpeg|max:$logoSize|dimensions:$logoKeys[0]=$logoWidth,$logoKeys[1]=$logoHeight",
                    'thumbnail' => "sometimes|mimes:png,jpg,jpeg|max:$thumbnailSize|dimensions:$thumbnailKeys[0]=$thumbnailWidth,$thumbnailKeys[1]=$thumbnailHeight",
                    'banner' => "sometimes|mimes:png,jpg,jpeg|max:$bannerSize|dimensions:$bannerKeys[0]=$bannerWidth,$bannerKeys[1]=$bannerHeight",
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
