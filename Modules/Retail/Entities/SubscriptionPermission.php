<?php

namespace Modules\Retail\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscriptionPermission extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'key', 'value', 'status'];
}
