<?php

namespace Modules\Retail\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class MailingRequest extends FormRequest
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
                $business_id = request()->business_id;
                return [
                    'title' => [
                        'required',
                        Rule::unique('mailings')->where(function ($query) use ($business_id) {
                            return $query->where('business_id', $business_id)->whereNull('deleted_at');
                        })
                    ],
                    'minimum_amount' => 'required|numeric',
                    'price' => 'required|numeric',
                ];
            case 'PUT':
                $business_id = request()->business_id;
                return [
                    'title' => [
                        'required',
                        Rule::unique('mailings')->where(function ($query) use ($business_id) {
                            return $query->where('business_id', $business_id);
                        })->ignore($this->mailing)
                    ],
                    'minimum_amount' => 'required|numeric',
                    'price' => 'required|numeric',
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
}
