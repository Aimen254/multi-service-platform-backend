<?php

namespace App\Rules;

use App\Models\Coupon;
use App\Models\Product;;

use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Rule;

class CheckCouponDiscountPrice implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $product;
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
        $coupon = Coupon::findOrfail($value);
        if ($coupon->discount_type == 'fixed') {
            return $this->product->price <= $coupon->discount_value ? \false : \true;
        } else {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The discount price of this coupon must be less than product actual price.';
    }
}
