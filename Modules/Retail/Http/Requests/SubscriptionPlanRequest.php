<?php

namespace Modules\Retail\Http\Requests;

use App\Models\StandardTag;
use Illuminate\Foundation\Http\FormRequest;

class SubscriptionPlanRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'price' => 'required',
            'interval' => 'required',
            'permissions.total_products.value' => 'required_if:permissions.total_products.status,true',
            'permissions.type.value' => 'required_if:permissions.type.status,true',
            'permissions.featured_businesses.value' => 'required_if:permissions.featured_businesses.status,true',
            'permissions.total_businesses.value' => 'required_if:permissions.total_businesses.status,true',


        ];
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

    public function messages()
    {
        $module = StandardTag::findOrFail($this->route('moduleId'));
        if (in_array($module->slug, ['retail', 'automotive', 'boats'])) {
            $message = 'products';
        } else {
            $message = $module->slug;
        }
        return [
            'permissions.total_products.value.required_if' => 'Total ' . $message . ' value is required.',
            'permissions.type.value.required_if' => 'The subscription type is required.',
            'permissions.featured_businesses.value.required_if' => 'Featured business value is required.',
            'permissions.total_businesses.value.required_if' => 'Total business value is required.'
        ];
    }
}
