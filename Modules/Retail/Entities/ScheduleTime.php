<?php

namespace Modules\Retail\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScheduleTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_schedule_id',
        'open_at',
        'close_at',
    ];

    /**
     * Get the business schedule that owns the scheduleTime.
     */
    public function businessschedule()
    {
        return $this->belongsTo(BusinessSchedule::class);
    }
    /**
     * Get the business time.
     *
     * @param  string  $value
     * @return string
     */
    public function getOpenAtAttribute($value)
    {
        return ucfirst(convertTime($value, 'h:i A'));
    }

    public function setOpenAtAttribute($value)
    {
        
        $this->attributes['open_at'] = convertTime($value, 'H:i A');
    }

    public function getCloseAtAttribute($value)
    {
        return ucfirst(convertTime($value, 'h:i A'));
    }

    public function setCloseAtAttribute($value)
    {
        $this->attributes['close_at'] = convertTime($value, 'H:i A');
    }
    
}
