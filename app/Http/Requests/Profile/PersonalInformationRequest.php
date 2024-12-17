<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class PersonalInformationRequest extends FormRequest
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
        $avatarKeys = array_keys($avatar);
        $width = $avatar['width'];
        $height = $avatar['height'];
        $size = $avatar['size'];
        $id = auth()->user()->id;
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required', 'email', 'unique:users,email,' . $id],
            'password' => ['nullable'],
            'avatar' => "sometimes|mimes:png,jpg,jpeg|max:$size|dimensions:$avatarKeys[0]=$width,$avatarKeys[1]=$height",
        ];
    }
    public function messages() {
        $avatar = config()->get('image.media.avatar');
        $width = $avatar['width'];
        $height = $avatar['height'];
        $size = $avatar['size'];
        return [
            'avatar.max' => "Avatar size should be $size kb",
            'avatar.dimensions' => "Avatar dimensions should be of maximum $width x $height"
        ];
    }
}
