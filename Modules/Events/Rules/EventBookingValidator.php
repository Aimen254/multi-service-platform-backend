<?php

namespace Modules\Events\Rules;

use Carbon\Carbon;
use App\Models\Product;
use Illuminate\Contracts\Validation\Rule;
use Modules\Events\Entities\EventBooking;

class EventBookingValidator implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $validationType = null;
    public function __construct($validationType)
    {
        $this->validationType = $validationType;
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
        // check existance of event in bookings table
        if (EventBooking::where('product_id', $value)->where('user_id', auth()->user()?->id)->exists()) {
            return false;
        } else {
            if ($this->validationType != 'check_existance') {
                // check date of event
                $eventDateTime = Product::where('id', $value)->with('events')->first();
                return Carbon::now()->gt(Carbon::parse($eventDateTime->events?->event_date)) ? false : true;
            }
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->validationType === 'check_existance' ? 'You cannot buy ticket for this event, you have booked this earlier.' : 'The event date has been passed.';
    }
}
