<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPriority extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['product_id', 'P1', 'P2', 'P3', 'P4'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'P1' => 'json',
        'P2' => 'json',
        'P3' => 'json',
        'P4' => 'json'
    ];
}
