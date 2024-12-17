<?php

namespace App\Http\Requests\API;

use Illuminate\Validation\Rule;
use App\Rules\CheckDeactivatedEmail;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $avatar = config()->get('image.media.avatar');
        $banner = config()->get('image.media.banner');

        $avatarKeys = array_keys($avatar);
        $bannerKeys = array_keys($banner);
        $width = $avatar['width'];
        $height = $avatar['height'];
        $size = $avatar['size'];
        $bannerWidth = $banner['width'];
        $bannerHeight = $banner['height'];
        $bannerSize = $banner['size'];

        switch (request()->getMethod()) {
            case 'POST':
                return [
                    'business_id' => 'required',
                    'user_type' => 'required|string|max:100',
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => ['required', 'email', Rule::unique('users', 'email')->whereNull('deleted_at'), new CheckDeactivatedEmail()],
                    'phone' => ['nullable','regex:/^\+\d{1,3}\d{9,}$/'],
                    'password' => ['required', 'min:8'],
                    'neighborhood_name' => 'nullable|string|max:220',
                    'avatar' => "sometimes|mimes:png,jpg,jpeg|max:$size|dimensions:$avatarKeys[0]=$width,$avatarKeys[1]=$height",
                    'addresses.*.name' => 'required_with:addresses.*.address',
                    'addresses.*.address' => 'required_with:addresses.*.name',
                ];
            case 'PUT':
                $id = $this->route('id');
                return [
                    'business_id' => 'required',
                    'user_type' => 'required|string|max:100',
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at'), new CheckDeactivatedEmail()],
                    'phone' => ['nullable','regex:/^\+\d{1,3}\d{9,}$/'],
                    'password' => ['nullable'],
                    'neighborhood_name' => 'nullable|string|max:220',
                    'avatar' => "sometimes|mimes:png,jpg,jpeg|max:$size|dimensions:$avatarKeys[0]=$width,$avatarKeys[1]=$height",
                    'cover_image' => "sometimes|mimes:png,jpg,jpeg|max:$bannerSize|dimensions:$bannerKeys[0]=$bannerWidth,$bannerKeys[1]=$bannerHeight",
                    'addresses.*.name' => 'required_with:addresses.*.address',
                    'addresses.*.address' => 'required_with:addresses.*.name',
                ];
        }
    }

    public function messages(): array
    {
        $avatar = config()->get('image.media.avatar');
        $width = $avatar['width'];
        $height = $avatar['height'];
        $size = $avatar['size'];

        return [
            'addresses.*.name.required_with' => 'Name field is required',
            'addresses.*.address.required_with' => 'Address field is required',
            'avatar.max' => "Avatar size must be $size kb",
            'avatar.dimensions' => "Avatar dimensions must be of $width x $height"
        ];
    }
}
