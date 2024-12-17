<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'business_id',
        'user_id',
        'recepient_id',
        'total',
        'coupon_applied',
        'actual_price',
        'discount_price',
        'discount_value',
        'discount_type',
        'tax',
        'coupon_id',
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
         * Handle the Cart "creating" event.
         *
         * @param  \App\Models\Cart  $Cart
         * @return void
         */
        static::creating(function (Cart $cart) {
            $cart->uuid = Str::uuid();
        });
    }

    public function items()
    {
        return $this->hasMany(CartItem::class)->orderBy('id', 'desc');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function recepient()
    {
        return $this->belongsTo(Recepient::class, 'recepient_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }
    public function recipient()
    {
        return $this->belongsTo(Recepient::class);
    }
}
