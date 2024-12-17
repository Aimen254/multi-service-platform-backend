<?php

namespace Modules\Obituaries\Rules;

use Illuminate\Contracts\Validation\Rule;

class DateOfBirth implements Rule
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
        // Assuming that $attribute contains 'date_of_birth' and $value contains the date of birth value
        $birthDate = request()->input('date_of_birth'); // Replace 'death_date' with your actual input field name for date of death
        $deathDate = request()->input('date_of_death');

        return $birthDate <= $deathDate;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Date of birth must be less than or equal to date of death.';
    }
}
