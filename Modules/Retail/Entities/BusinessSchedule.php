<?php

namespace Modules\Retail\Entities;

use App\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'status',
    ];

    /**
     * Get the business that owns the businessSchedule.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the Schedule Time for the business schedule.
     */
    public function scheduletimes()
    {
        return $this->hasMany(ScheduleTime::class);
    }

    /**
     * Get the business schedule that owns the scheduleTime.
     */
    public function businessschedule()
    {
        return $this->belongsTo(BusinessSchedule::class);
    }

    public function statusChanger()
    {
        $this->status = $this->status == 'active' ? 'inactive' : 'active';
        return $this;
    }
}
