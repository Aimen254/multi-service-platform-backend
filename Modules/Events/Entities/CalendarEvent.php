<?php

namespace Modules\Events\Entities;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'date',
        'status',
        'product_id',
        'module_id',
        'user_id',
    ];

    // scope
    public function scopeActive($query)
    {
        return $query->whereHas('product', function ($subQuery) {
            $subQuery->where('status', 'active')
                ->whereRelation('user', 'status', 'active');
        });
    }

    public function scopeWhereEventDateNotPassed($query)
    {
        return $query->where('date', '>=', Carbon::now());
    }

    // relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
