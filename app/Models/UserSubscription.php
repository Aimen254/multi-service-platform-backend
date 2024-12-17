<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Retail\Entities\SubscriptionPermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module_id',
        'product_id',
    ];

    public function subscriptionPermissions()
    {
        return $this->hasMany(SubscriptionPermission::class, 'product_id', 'product_id');
    }
}
