<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_name',
        'user_id',
        'email',
        'payment_method_id',
        'brand',
        'country',
        'expiry_month',
        'expiry_year',
        'last_four',
        'live_mode',
        'token',
        'default',
        'customer_id',
        'save_card',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


