<?php

namespace App\Http\Requests\API;

use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Rules\CheckDeactivatedEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class RegistrationRequest extends FormRequest
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
        $avatarKeys = array_keys($avatar);
        $width = $avatar['width'];
        $height = $avatar['height'];
        $size = $avatar['size'];
        if (request()->is_social) {
            return [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['nullable', 'string', 'max:255'],
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->where(function ($query) {
                        $query->whereNull('deleted_at')->where('is_social', 0);
                    }),
                    new CheckDeactivatedEmail()
                ],
                'provider' => ['required', 'in:facebook,google,apple'],
                'provider_id' => ['required'],
            ];
        } else {
            return [
                'first_name' => 'required|alpha',
                'last_name' => 'required|alpha',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->whereNull('deleted_at'), new CheckDeactivatedEmail()
                ],
                'password' => ['required', 'min:8'],
                'password_confirmation' => ['required', 'min:8', 'same:password'],
                'phone' => ['nullable', 'regex:/^\+\d{1,3}\d{9,}$/'],
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

    public function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            'message' => $validator->errors()->first()
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY,);

        throw new ValidationException($validator, $response);
    }
}
