<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PriceValidation implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $price = request()->input('price');
        $maxPrice = request()->input('max_price');
        if ($price !== null && $maxPrice !== null) {
            if ($price > $maxPrice) {
                return false; 
            }
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
        return 'minimum price never be greater than maximum price.';
    }
}
