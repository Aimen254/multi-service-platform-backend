<?php

namespace Modules\Retail\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessAdditionalEmail extends Model
{
    use HasFactory;
    protected $fillable = [
        'personal_name', 'email', 'business_id', 'title'
    ];
}
