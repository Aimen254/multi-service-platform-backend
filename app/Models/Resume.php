<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'location',
        'experience',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resumes()
    {
        return $this->morphOne(Media::class, 'model');
    }
}
