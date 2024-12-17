<?php

namespace Modules\Events\Entities;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventBooking extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->whereHas('product', function ($subQuery) {
            $subQuery->where('status', 'active')->whereRelation('user', 'status', 'active');
        });
    }

    // product relation
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
