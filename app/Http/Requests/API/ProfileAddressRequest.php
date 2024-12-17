<?php

namespace App\Http\Requests\API;

use App\Enums\AddressType;
use App\Rules\CheckAddressStatus;
use BenSampo\Enum\Rules\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ProfileAddressRequest extends FormRequest
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
        return [
            'id' => 'nullable|exists:addresses',
            'name' => 'required',
            'street_address' => 'nullable',
            'address' => 'required',
            // 'latitude' => 'required',
            // 'longitude' => 'required',
            'note' => ['nullable', 'string', 'max:255'],
            'type' => ['required', new EnumValue(AddressType::class)],
            // 'status' => ['nullable', 'in:active,inactive', new CheckAddressStatus()],
            'is_default' => ['nullable']
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
}
