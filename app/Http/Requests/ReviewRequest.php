<?php

namespace App\Http\Requests;

use App\Models\StandardTag;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ReviewRequest extends FormRequest
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
        $moduleTagId = request()->route('module_id');
        $module = StandardTag::where('id', $moduleTagId)->orWhere('slug', $moduleTagId)->first();
        $module = $module->slug;
        switch (request()->getMethod()) {
            case 'GET':
                return [
                    'type' => 'required',
                    'id' => [Rule::requiredIf($module == 'retail' || $module == 'boats'), 'integer'],
                    'make_id' => [Rule::requiredIf(($module == 'automotive') && $this->type == 'product')],
                    'model_id' => [Rule::requiredIf(($module == 'automotive') && $this->type == 'product')],
                    'year' => ['nullable'],
                    'year_min' => ['nullable'],
                    'year_max' => ['nullable'],
                ];
            case 'POST':
            case 'PUT':
                switch ($module) {
                    case 'retail':
                    case 'boats':
                    case 'blogs':
                    case 'recipes':
                    case 'services':
                    case 'marketplace':
                    case 'taskers':
                    case 'employment':
                    case 'government':
                        return [
                            'rating' => 'required|max:5|numeric|min:1',
                            'comment' => 'required|max:500',
                            'user_id' => ['required', 'exists:users,id'],
                            'model_id' => ['required', 'integer'],
                            'type' => 'required'
                        ];
                    case 'automotive':
                        return [
                            'comment' => 'required|max:500',
                            'make_id' => ['required', 'exists:standard_tags,id'],
                            'model_id' => ['required', 'exists:standard_tags,id'],
                            'user_id' => ['required', 'exists:users,id'],
                            'year' => ['required'],
                            'status' => ['nullable'],
                            'overall_rating' => ['nullable'],
                            'comfort' => ['required', 'integer', 'min:1', 'max:5'],
                            'interior_design' => ['required', 'integer', 'min:1', 'max:5'],
                            'performance' => ['required', 'integer', 'min:1', 'max:5'],
                            'value_for_the_money' => ['required', 'integer', 'min:1', 'max:5'],
                            'exterior_styling' => ['required', 'integer', 'min:1', 'max:5'],
                            'reliability' => ['required', 'integer', 'min:1', 'max:5'],
                            'title' => ['required'],
                            'recommendation' => ['nullable'],
                            'condition' => ['nullable'],
                            'purpose' => ['nullable'],
                            'reviewer' => ['nullable'],
                            'type' => 'required'
                        ];
                }
        }
    }

    public function messages()
{
    return [
        'exterior_styling.required' => 'The exterior design field is required.',
         'value_for_the_money.required'=>'The value for  money field is required'
     ];
}

    public function prepareForValidation()
    {
        $moduleTagId = request()->route('module_id');
        $module = StandardTag::where('id', $moduleTagId)->orWhere('slug', $moduleTagId)->first();
        $module = $module->slug;
        switch ($module) {
            case 'automotive':
                switch ($this->getMethod()) {
                    case 'GET':
                        $this->merge([
                            'make_id' => $this->make_id && $this->make_id != 'default' ?  $this->make_id : null,
                            'model_id' => $this->model_id && $this->model_id != 'default' ? $this->model_id : null,
                        ]);
                        break;
                    case 'POST':
                    case 'PUT':
                        $make = StandardTag::where('id', $this->make_id)
                            ->orWhere(function ($query) {
                                $query->where('slug', $this->make_id)
                                    ->whereNotExists(function ($subquery) {
                                        $subquery->from('standard_tags')
                                            ->where('id', $this->make_id);
                                    });
                            })
                            ->firstOrFail();
                        $model = StandardTag::where('id', $this->model_id)
                            ->orWhere(function ($query) {
                                $query->where('slug', $this->model_id)
                                    ->whereNotExists(function ($subquery) {
                                        $subquery->from('standard_tags')
                                            ->where('id', $this->model_id);
                                    });
                            })
                            ->firstOrFail();
                        $this->merge([
                            'user_id' => $this->user()->id ?? $this->user()->id,
                            'make_id' => $this->make_id && $this->make_id != 'default' ? $make->id : null,
                            'model_id' => $this->model_id && $this->model_id != 'default' ? $model->id : null,
                        ]);
                        break;
                }
                break;
        }
    }

    public function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            'message' => $validator->errors()
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY,);

        throw new ValidationException($validator, $response);
    }
}
