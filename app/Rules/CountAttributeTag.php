<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CountAttributeTag implements Rule
{

    protected $attributeName = null;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($attributeName)
    {
        $this->attributeName = $attributeName;
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
        return count($value) > 1 ? false : true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Add only one tag of ' . $this->attributeName . ' attribute';
    }
}
