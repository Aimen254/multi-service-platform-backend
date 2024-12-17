<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MediaRule implements Rule
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
        $pattern = '/\.(jpg|jpeg|png|gif|mp4|mov|avi)/i';
        return !preg_match($pattern, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute cannot contain images, GIFs, or videos.';
    }
}
