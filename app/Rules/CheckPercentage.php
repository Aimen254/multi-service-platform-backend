<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckPercentage implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($type, $params = null)
    {
        $this->index = $params; //for businessSettings
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
        switch ($this->type) {
            case 'businessSettings':
                if (request()->settings[$this->index]['value'] == 'Platform fee in percentage') 
                {
                    if ($value > 100 || $value < 0) {
                        return false;
                    }
                    else{
                        return true;
                    }
                }
                return true;
                break;

            case 'coupon':
                if (request()->discount_type == 'percentage') {
                    if ($value > 100 || $value < 0) {
                        return false;
                    }
                    else{
                        return true;
                    }
                }
                return true;
                break;
            
            default:
                if ($value > 100 || $value < 0) {
                    return false;
                }
                else{
                    return true;
                }
                break;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Value must not be greater than 100 and lesser than 0.';
    }
}
