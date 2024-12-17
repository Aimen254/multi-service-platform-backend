<?php

namespace Modules\Retail\Http\Requests;

use App\Rules\CheckPercentage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class CouponRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $business = getBusinessDetails(request()->businessUuid);
        switch (request()->getMethod()) { 
            case 'POST':
                return [
                    'code' => ['required','unique:coupons,code','min:6', 'max:10'],
                    'end_date' =>  ['required',  'date','after:yesterday'],
                    'discount_type' =>  ['required', 'in:fixed,percentage'],
                    'discount_value' =>  ['required','integer', new CheckPercentage('coupon')],
                ];
            case 'PUT':
                $id = $this->route('coupon');
                return [
                    'code' => ['required','unique:coupons,code,'. $id, 'min:6', 'max:10'],
                    'end_date' =>  ['required','date','after:yesterday'],
                    'discount_type' =>  ['required', 'in:fixed,percentage'],
                    'discount_value' =>  ['required','integer', new CheckPercentage('coupon')],
                ];
        }
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

    public function messages()
    {
        return [
            'start_date.after' => 'Start date must not be less than current date.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $guard = Auth::getDefaultDriver();
        
        if ($guard != 'web') {
            $response = new JsonResponse([
                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY,);
            throw new ValidationException($validator, $response);
        }

        parent::failedValidation($validator);
    }
}
