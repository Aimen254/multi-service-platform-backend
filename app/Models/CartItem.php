<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Modules\Retail\Entities\ProductVariant;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'unit_price',
        'actual_price',
        'discount_price',
        'total',
        'tax',
        'business_id',
        'product_price',

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
         * Handle the CartItem "creating" event.
         *
         * @param  \App\Models\CartItem  $CartItem
         * @return void
         */
        static::creating(function (CartItem $cartItem) {
            $cartItem->uuid = Str::uuid();
        });

        /**
         * Handle the CartItem "created" event.
         *
         * @param  \App\Models\CartItem  $CartItem
         * @return void
         */
        static::created(function (CartItem $cartItem) {
            $cart = $cartItem->cart;
            $cart->update([
                'actual_price' => $cart->items()->sum('actual_price'),
                'total' => $cart->items()->sum('total') ,
                'discount_price' => $cart->coupon_id 
                    ? (
                        $cart->coupon->coupon_type != 'business'
                        ? $cart->items()->sum('discount_price') : $cart->discount_price
                    )
                    : $cart->discount_price
            ]);
        });

        /**
         * Handle the CartItem "updated" event.
         *
         * @param  \App\Models\CartItem  $CartItem
         * @return void
         */
        static::updated(function (CartItem $cartItem) {
            // $cart = $cartItem->cart;
            // $cart->update([
            //     'actual_price' => $cart->items()->sum('actual_price'),
            //     'total' => $cart->items()->sum('total'),
            //     'tax' => $cart->items()->sum('tax'),
            //     'discount_price' => $cart->coupon_id
            //         ? (
            //             $cart->coupon->coupon_type != 'business'
            //             ? $cart->items()->sum('discount_price') : $cart->discount_price
            //         )
            //         : $cart->discount_price
            // ]);
        });

        /**
         * Handle the CartItem "deleted" event.
         *
         * @param  \App\Models\CartItem  $CartItem
         * @return void
         */
        static::deleted(function (CartItem $cartItem) {
            // $cart = $cartItem->cart;
            // $cart->update([
            //     'actual_price' => $cart->items()->sum('actual_price'),
            //     'total' => $cart->items()->sum('total'),
            //     'tax' => $cart->items()->sum('tax')
            // ]);
        });
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
