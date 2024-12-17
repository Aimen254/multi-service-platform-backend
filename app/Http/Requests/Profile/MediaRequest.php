<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class MediaRequest extends FormRequest
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
        switch (request()->type) {
            case 'avatar':
                $avatar = config()->get('image.media.avatar');
                $avatarKeys = array_keys($avatar);
                $width = $avatar['width'];
                $height = $avatar['height'];
                $size = $avatar['size'];
                return [
                    "avatar" => "sometimes|mimes:png,jpg,jpeg|max:$size|dimensions:$avatarKeys[0]=$width,$avatarKeys[1]=$height",
                ];
            case 'logo':
                $logo = config()->get('image.media.logo');
                $logoKeys = array_keys($logo);
                $width = $logo['width'];
                $height = $logo['height'];
                $size = $logo['size'];
                return [
                    "image" => "sometimes|mimes:png,jpg,jpeg|max:$size|dimensions:$logoKeys[0]=$width,$logoKeys[1]=$height",
                ];
            case 'thumbnail':
                $thumbnail = config()->get('image.media.thumbnail');
                $thumbnailKeys = array_keys($thumbnail);
                $width = $thumbnail['width'];
                $height = $thumbnail['height'];
                $size = $thumbnail['size'];
                return [
                    "image" => "sometimes|mimes:png,jpg,jpeg|max:$size|dimensions:$thumbnailKeys[0]=$width,$thumbnailKeys[1]=$height",
                ];
            case 'banner':
                $banner = config()->get('image.media.banner');
                $bannerKeys = array_keys($banner);
                $width = $banner['width'];
                $height = $banner['height'];
                $size = $banner['size'];
                return [
                    "image" => "sometimes|mimes:png,jpg,jpeg|max:$size|dimensions:$bannerKeys[0]=$width,$bannerKeys[1]=$height",
                ];
            case 'secondaryBanner':
                $secondaryBanner = config()->get('image.media.secondaryBanner');
                $secondaryBannerKeys = array_keys($secondaryBanner);
                $width = $secondaryBanner['width'];
                $height = $secondaryBanner['height'];
                $size = $secondaryBanner['size'];
                return [
                    "image" => "sometimes|mimes:png,jpg,jpeg|max:$size|dimensions:$secondaryBannerKeys[0]=$width,$secondaryBannerKeys[1]=$height",
                ];
        }
    }
    public function messages() {
        switch (request()->type) {
            case 'avatar':
                $avatar = config()->get('image.media.avatar');
                $width = $avatar['width'];
                $height = $avatar['height'];
                $size = $avatar['size'];
                return [
                    'avatar.max' => "Avatar size must be $size kb",
                    'avatar.dimensions' => "Avatar dimensions must be of $width x $height"
                ];
            case 'logo':
                $logo = config()->get('image.media.logo');
                $width = $logo['width'];
                $height = $logo['height'];
                $size = $logo['size'];
                return [
                    'image.max' => "Logo size must be $size kb",
                    'image.dimensions' => "Logo dimensions must be of $width x $height"
                ];
            case 'thumbnail':
                $thumbnail = config()->get('image.media.thumbnail');
                $width = $thumbnail['width'];
                $height = $thumbnail['height'];
                $size = $thumbnail['size'];
                return [
                    'image.max' => "Thumbnail size must be $size kb",
                    'image.dimensions' => "Thumbnail dimensions must be of $width x $height"
                ];
            case 'banner':
                $banner = config()->get('image.media.banner');
                $width = $banner['width'];
                $height = $banner['height'];
                $size = $banner['size'];
                return [
                    'image.max' => "Banner size must be $size kb",
                    'image.dimensions' => "Banner dimensions must be of $width x $height"
                ];
            case 'secondaryBanner':
                $secondaryBanner = config()->get('image.media.secondaryBanner');
                $width = $secondaryBanner['width'];
                $height = $secondaryBanner['height'];
                $size = $secondaryBanner['size'];
                return [
                    'image.max' => "Banner size must be $size kb",
                    'image.dimensions' => "secondaryBanner dimensions must be of $width x $height"
                ];
        }
    }
}
