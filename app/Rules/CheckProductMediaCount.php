<?php

namespace App\Rules;

use App\Models\Business;;

use App\Models\Product;
use Illuminate\Contracts\Validation\Rule;

class CheckProductMediaCount implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $type;
    public function __construct($type)
    {
        $this->type = $type;
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
        if ($this->type == 'business') {

            $business = Business::withCount('secondaryImages')->findOrFail(request()->id);
            return $business->secondary_images_count < 4 ?  \true : \false;
        } else {
            $product = Product::whereUuid(request()->uuid)->withCount('secondaryImages')->firstOrFail();
            return $product->secondary_images_count < 7 ?  \true : \false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->type == 'business' ?  'You can add 3 images.' : 'You can add 6 images.';
    }
}
