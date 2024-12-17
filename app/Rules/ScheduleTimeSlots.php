<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Retail\Entities\ScheduleTime;

class ScheduleTimeSlots implements Rule
{
    private $data;
    private $id;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $data = null)
    {
        $this->data = $data;
        $this->id = $this->data['schedule_id'];
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
        $id = $this->id;
        $businessScheduleId = $this->data['business_schedule_id'];
        $openAt = $this->data['open_at'] ? convertTime($this->data['open_at'], 'H:i A') : '00:00 AM';
        $closeAt = $this->data['close_at'] ? convertTime($this->data['close_at'], 'H:i A') : '00:00 AM';
        $time = ScheduleTime::where(function ($query) use ($openAt, $closeAt) {
            $query->where(function ($query) use ($openAt, $closeAt) {
                $query->where('open_at', '<=', $openAt)
                    ->where('close_at', '>', $openAt);
            })
            ->orWhere(function ($query) use ($openAt, $closeAt) {
                $query->where('open_at', '<', $closeAt)
                    ->where('close_at', '>=', $closeAt);
            });
        })
        ->where(function ($query) {
            if ($this->id) {
                $query->whereNotIn('id', [$this->id]);
            }
        })->where('business_schedule_id', $businessScheduleId)->first();
        return $time ? \false : \true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This time slot has already been taken.';
    }
}
