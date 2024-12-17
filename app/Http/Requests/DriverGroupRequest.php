<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DriverGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \auth()->check();
    }

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
                    'name' => ['required', 'unique:groups'],
                    'manager_id' => ['required', 'exists:users,id']
                ];
                break;
            case 'PUT':
                $id = $this->route('group');
                return [
                    'name' => ['required', 'unique:groups,name,' . $id],
                    'manager_id' => ['required', 'exists:users,id']
                ];
        }
    }
}
