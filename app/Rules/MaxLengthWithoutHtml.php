<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class MaxLengthWithoutHtml implements Rule
{
    private $maxLength;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($maxLength)
    {
        $this->maxLength = $maxLength;
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
        $cleanValue = Str::of(strip_tags($value))->trim();

        return $cleanValue->length() <= $this->maxLength;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The :attribute field must not be greater than {$this->maxLength} characters.";
    }
}
