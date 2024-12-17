<?php

namespace App\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ConversationRequest extends FormRequest
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
        return [
            'sender_id' => 'required',
            'subject' => ['required'],
            'comment' => ['required'],
            'phone_number' => ['required', 'regex:/^\+\d{1,3}\d{9,}$/'],
            'message' => 'required',
        ];
    }


    public function messages()
    {
        return [
            'sender_id.required' => "Sender is required.",
            'message.required' => 'Message is Required.'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            'message' => $validator->errors(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY,);
        throw new ValidationException($validator, $response);
        // use default behavior for web.php routes
        parent::failedValidation($validator);
    }
}
