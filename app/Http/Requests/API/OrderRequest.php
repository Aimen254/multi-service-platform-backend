<?php

namespace App\Http\Requests\Api;

use App\Rules\UserCardValidation;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class OrderRequest extends FormRequest
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
            'orderType' => ['required', 'in:pick_up,delivery,mail'],
            'shipping' => [
                'nullable',
                'required_if:orderType,delivery,mail',
                'exists:addresses,id'
            ],
            'billing' => ['nullable', 'exists:addresses,id'],
            'delivery_fee' => ['nullable', 'required_if:orderType,delivery,mail'],
            'selected_card' => [
                'nullable',
                'required_if:payment_method,credit-card',
                'exists:credit_cards,id',
                new UserCardValidation()
            ],
            'mailing_id' => ['nullable', 'required_if:orderType,mail', 'exists:mailings,id'],
            'business_id' => ['required', 'exists:businesses,id'],
            'discount_price' => ['required']
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->input('delivery_fee')) {
            $delivery_fee = $this->input('delivery_fee');
            // Remove commas and format as float
            $delivery_fee = floatval(str_replace(',', '', $delivery_fee));
            // Merge the modified price back into the request
            $this->merge(['delivery_fee' => $delivery_fee]);
            // Now check the type of the input
        }
    }

    public function messages()
    {
        return [
            'selected_card.required_if' => 'Credit card is required.'
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
