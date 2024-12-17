<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Rules\CheckDeactivatedEmail;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
        switch (request()->getMethod()) {
            case 'POST':
                return [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => ['required', 'email', Rule::unique('users', 'email')->whereNull('deleted_at'), new CheckDeactivatedEmail()],
                    'password' => ['required', 'min:8'],
                    'avatar' => "sometimes|mimes:png,jpg,jpeg|max:$size|dimensions:$avatarKeys[0]=$width,$avatarKeys[1]=$height",
                    'addresses.*.name' => 'required_with:addresses.*.address',
                    'addresses.*.address' => 'required_with:addresses.*.name',
                ];
            case 'PUT':
                $id = $this->route('employee');
                return [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at'), new CheckDeactivatedEmail()],
                    'password' => ['nullable'],
                    'avatar' => "sometimes|mimes:png,jpg,jpeg|max:$size|dimensions:$avatarKeys[0]=$width,$avatarKeys[1]=$height",
                    'addresses.*.name' => 'required_with:addresses.*.address',
                    'addresses.*.address' => 'required_with:addresses.*.name',
                ];
        }
    }
    public function messages()
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
