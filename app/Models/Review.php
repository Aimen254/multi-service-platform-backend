<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Automotive\Entities\VehicleReview;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'model_id',
        'user_id',
        'module_id',
        'model_type',
        'order_id',
        'reviewer_id',
        'rating',
        'comment',
        'status',
        'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function statusChanger()
    {
        $this->status = $this->status == 'active' ? 'inactive' : 'active';
        return $this;
    }
    /**
     * Get all of the models that own reviews.
     */
    public function model()
    {
        return $this->morphTo();
    }

    public function product() {
        return $this->belongsTo(Product::class, 'model_id', 'id');
    }

    public function vehicleReview()
    {
        return $this->hasOne(VehicleReview::class);
    }
}
