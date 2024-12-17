<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckSearchLevels implements Rule
{
    public $errorMessage = '';
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
        if ($attribute == 'L2') {
            $this->errorMessage = 'Please select L1.';
            return \request()->filled('L1') ? \true : \false;
        } else if ($attribute == 'L3') {
            $this->errorMessage = 'Please select L2.';
            return \request()->filled('L2') && \request()->filled('L1') ? \true : \false;
        } else {
            return \true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage;
    }
}
