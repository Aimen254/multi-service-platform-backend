<?php

namespace Modules\Retail\Http\Requests;

use App\Rules\ScheduleTimeSlots;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class BusinessScheduleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->getMethod()) {
            case 'POST':
                return [
                    'open_at' => 'required|date_format:h:i A',
                    'close_at' => [
                        'required',
                        'date_format:h:i A',
                        new ScheduleTimeSlots($this->all())
                    ],
                ];
            case 'PUT':
                return [
                    'open_at' => 'required|date_format:h:i A',
                    'close_at' => [
                        'required',
                        'date_format:h:i A',
                        new ScheduleTimeSlots($this->all())
                    ],
                ];
            default:
                return [];
        }
    }



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

