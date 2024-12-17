<?php

namespace Modules\Government\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\CheckDeactivatedEmail;

class GovernmentStaffRequest extends FormRequest
{
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
                    'phone' => 'nullable|string|max:100',
                    'neighborhood_name' => 'nullable|string|max:220',
                    'avatar' => "sometimes|mimes:png,jpg,jpeg|max:$size|dimensions:$avatarKeys[0]=$width,$avatarKeys[1]=$height",
                    'phone' => 'nullable'
                ];
            case 'PUT':
                $id = $this->route('staff');
                return [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at'), new CheckDeactivatedEmail()],
                    'password' => ['nullable'],
                    'phone' => 'nullable|string|max:100',
                    'neighborhood_name' => 'nullable|string|max:220',
                    'avatar' => "sometimes|mimes:png,jpg,jpeg|max:$size|dimensions:$avatarKeys[0]=$width,$avatarKeys[1]=$height",
                    'phone' => 'nullable'
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
            'avatar.max' => "Avatar size must be $size kb",
            'avatar.dimensions' => "Avatar dimensions must be of $width x $height"
        ];
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
