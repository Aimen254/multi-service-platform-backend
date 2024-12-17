<?php

namespace App\Rules;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class CheckUserType implements Rule
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
        $user = User::whereEmail($value)->whereUserType('customer')->first();
        return $user ? \true : \false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Selected email is invalid';
    }
}
