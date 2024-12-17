<?php

namespace Modules\Boats\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class DealershipRequest extends FormRequest
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
                    'name' => 'required',
                    'slug' => ['required', 'without_spaces', 'unique:businesses,slug'],
                    'email' => ['required'],
                    'owner_id' => ['nullable', 'exists:users,id'],
                    'address' => Rule::requiredIf(function (){
                        return request()->user()->hasRole(['business_owner', 'admin', 'newspaper']);
                    }),
                    'city' => Rule::requiredIf(function (){
                        return request()->user()->hasRole(['customer']);
                    }),
                    'home_delivery' => 'nullable',
                    'virtual_appointments' => 'nullable',
                    'phone' => Rule::requiredIf(function (){
                        return request()->user()->hasRole(['business_owner', 'admin', 'newspaper']) ;
                    }),
                    'status' =>  'nullable',
                ];
            case 'PUT':
                $businessId = request()->input('id');
                return [
                    'name' => 'required',
                    'slug' => ['required', 'without_spaces', 'unique:businesses,slug,' . $businessId],
                    'email' => ['required'],
                    'owner_id' => ['nullable', 'exists:users,id'],
                    'address' => Rule::requiredIf(function (){
                        return request()->user()->hasRole(['business_owner', 'admin', 'newspaper']);
                    }),
                    'city' => Rule::requiredIf(function (){
                        return request()->user()->hasRole(['customer']);
                    }),
                    'home_delivery' => 'nullable',
                    'virtual_appointments' => 'nullable',
                    'phone' => Rule::requiredIf(function (){
                        return request()->user()->hasRole(['business_owner', 'admin', 'newspaper']);
                    }),
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
        return auth()->check();
    }

    public function prepareForValidation()
    {
        $this->merge([
            'owner_id' => $this->input('owner_id') ?? $this->user()->id,
        ]);
    }

    public function failedValidation(Validator $validator)
    {
        $routeName = $this->route()->getName();
        if (strpos($routeName, 'businesses.store') !== false || strpos($routeName, 'businesses.update') !== false) {
            $response = new JsonResponse([ 
                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first()
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY,);
            throw new ValidationException($validator, $response);
        }
        // use default behavior for web.php routes
        parent::failedValidation($validator);
    }
}
