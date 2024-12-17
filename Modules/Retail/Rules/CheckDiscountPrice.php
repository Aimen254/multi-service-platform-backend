<?php

namespace Modules\Retail\Rules;

use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Rule;

class CheckDiscountPrice implements Rule
{
    protected $product;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->product = Product::whereUuid(Route::current()->parameters['uuid'])->firstOrFail();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (request()->discount_type == 'fixed') {
            return $this->product->price <= $value ? \false : \true;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The discount price must be less than product actual price.';
    }
}
