<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Retail\Entities\BusinessHoliday;

class UniqueDates implements Rule
{
    private $id;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id = NULL)
    {
        $this->id = $id;
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
        $check = true;
        $id = $this->id;
        foreach ($value as $key => $date){
            $date = BusinessHoliday::where('business_id', request()->business_id)->whereRaw('FIND_IN_SET(?, date)', [$date['id']])
            ->where(function ($query) {
                if ($this->id) {
                    $query->whereNotIn('id', [$this->id]);
                }
            })->first();
            if ($date) {
                $check = false;
                break;
            }
        }
        return $check;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The date must be unique and one of the selected date already exist.';
    }
}
