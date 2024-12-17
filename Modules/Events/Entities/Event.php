<?php

namespace Modules\Events\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'event_location',
        'away_team',
        'event_ticket',
        'performer',
        'event_date'

    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];
}
