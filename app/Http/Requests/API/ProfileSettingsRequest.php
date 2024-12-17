<?php

namespace App\Http\Requests\API;

use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Rules\CheckDeactivatedEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ProfileSettingsRequest extends FormRequest
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

        $avatar = config()->get('image.media.avatar');
        $banner = config()->get('image.media.banner');
        $avatarKeys = array_keys($avatar);
        $bannerKeys = array_keys($banner);

        $width = $avatar['width'];
        $height = $avatar['height'];
        $size = $avatar['size'];
        $cover_width =  $banner['width'];
        $coverImg_height =  $banner['height'];
        $coverImg_size =  $banner['size'];
        $id = auth()->user()->id;
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at'), new CheckDeactivatedEmail()],
            'phone' => ['required', 'regex:/^\+\d{1,3} \d{3} \d{3} \d{4}$/'],
            'avatar' => "sometimes|mimes:png,jpg,jpeg|max:$size|dimensions:$avatarKeys[0]=$width,$avatarKeys[1]=$height",
            'cover_img' => "sometimes|image|mimes:png,jpg,jpeg|max:  $coverImg_size|dimensions:$bannerKeys[0]=  $cover_width,$bannerKeys[1]=   $coverImg_height"
        ];
    }

    public function messages()
    {
        $avatar = config()->get('image.media.avatar');
        $width = $avatar['width'];
        $height = $avatar['height'];
        $size = $avatar['size'];
        $cover_img = config()->get('image.media.banner');
        $coverImg_width =  $cover_img['width'];
        $coverImg_height = $cover_img['height'];
        $bannerImg_size =  $cover_img['size'];
        return [
            'avatar.max' => "Avatar size must be $size kb",
            'avatar.dimensions' => "Avatar dimensions must be of $width x $height",
            'cover_img.max' => "Cover image size must be $bannerImg_size kb",
            'cover_img.dimensions' => "Cover image dimensions must be of  $coverImg_width x $coverImg_height",
        ];
    }


   

    public function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            'message' => $validator->errors()
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY,);

        throw new ValidationException($validator, $response);
    }
}
