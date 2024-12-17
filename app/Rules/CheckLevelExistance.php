<?php

namespace App\Rules;

use App\Models\StandardTag;
use App\Models\TagHierarchy;
use Illuminate\Contracts\Validation\Rule;

class CheckLevelExistance implements Rule
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
        $tag = StandardTag::where('id', $value)->orWhere('slug', $value)->first();
        switch ($attribute) {
            case 'L1':
                $this->errorMessage = 'Selected L1 is invalid';
                return $tag ? (TagHierarchy::where('L1', $tag->id)->exists() ? true : false) : false;
                break;
            case 'L2':
                $this->errorMessage = 'Selected L2 is invalid';
                return $tag ? (TagHierarchy::where('L2', $tag->id)->exists() ? true : false) : false;
                break;
            default:
                $this->errorMessage = 'Selected L3 is invalid';
                return $tag ? (TagHierarchy::where('L3', $tag->id)->exists() ? true : false) : false;
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
        return $this->errorMessage;
    }
}
