<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductViews extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'ip_address',
        'module_id',
        'user_id'
    ];
}
