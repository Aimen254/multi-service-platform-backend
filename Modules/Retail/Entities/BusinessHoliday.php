<?php

namespace Modules\Retail\Entities;

use App\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessHoliday extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'title', 'date'];

    /**
     * Business Holiday Date .
     *
     * @param  string  $value
     * @return string
     */

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = CommaSeparateDateValues($value);
    }

    protected $appends = ['formatted_date'];

    public function getFormattedDateAttribute()
    {
        $dates = explode(',', $this->attributes['date']);
        $dates = array_map('trim', $dates);
        usort($dates, function ($a, $b) {
            return Carbon::parse($a)->gt(Carbon::parse($b));
        });
        $formattedDates = array_map(function ($date) {
            return Carbon::parse($date)->isoFormat('DD MMMM YYYY');
        }, $dates);

        return implode(', ', $formattedDates);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
