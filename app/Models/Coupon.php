<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $fillable = [
        'model_id',
        'model_type',
        'product_id',
        'code',
        'minimum_purchase',
        'limit',
        'start_date',
        'end_date',
        'coupon_type',
        'discount_value',
        'discount_type',
        'status',
        'created_by',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::boot();

        /**
         * Handle the Coupon "updated" event.
         *
         * @param  \App\Models\Coupon  $coupon
         * @return void
         */
        static::updating(function (Coupon $coupon) {
            // if ($coupon->discount_type == "fixed") {
            //     foreach ($coupon->products as $product) {
            //         if ($coupon->discount_value > $product->price) {
            //             $product->coupons()->detach($coupon->id);
            //         } 
            //     }
            // }
        });

        /**
         * Handle the Coupon "updated" event.
         *
         * @param  \App\Models\Coupon  $coupon
         * @return void
         */
        static::updated(function (Coupon $coupon) {
            foreach ($coupon->products as $product) {
                if ($coupon->status == 'active' && $product->pivot->previous_status != 'inactive') {
                    $attribute = ['status' => 'active'];
                    $product->coupons()->updateExistingPivot($coupon->id, $attribute);
                } else {
                    $attribute = ['status' => 'inactive'];
                    $product->coupons()->updateExistingPivot($coupon->id, $attribute);
                }
            }
        });
    }

    public function statusChanger()
    {
        $this->status = $this->status == 'active' ? 'inactive' : 'active';
        return $this;
    }

    /**
     * Get the parent model
     */
    public function model()
    {
        return $this->morphTo();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('status', 'previous_status');
    }


    /**
     * set Date .
     *
     * @param  string  $value
     * @return string
     */

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = setDateValues($value);
    }

   

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}
