<?php

namespace Modules\Retail\Http\Requests;

use App\Rules\UniqueDates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class BusinessHolidayRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch (request()->getMethod()) {
            case 'POST':
                return [
                    'title' => 'required',
                    'date' => [
                        'required',
                        new UniqueDates()
                    ],
                ];
            case 'PUT':
                $id = request()->id;
                return [
                    'title' => 'required',
                    'date' => [
                        'required',
                        new UniqueDates($id)
                    ],
                ];
        }
    }

    protected function prepareForValidation()
    {
        $guard = Auth::getDefaultDriver();
        if (request()->input('date') && $guard != 'web') {
            $dateArray = json_decode(request()->input('date'), true);
            $this->merge(['date' => $dateArray]);
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
