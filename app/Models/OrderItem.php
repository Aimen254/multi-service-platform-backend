<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Modules\Retail\Entities\ProductVariant;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'coupon_id',
        'price',
        'quantity',
        'total',
        'tax_value',
        'tax_price',
        'actual_price',
        'discount_price',
        'refunded',
        'product_variant_id',
        'color',
        'size',
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
         * Handle the OrderItem "created" event.
         *
         * @param  \App\Models\OrderItem  $OrderItem
         * @return void
         */
        static::created(function (OrderItem $orderItem) {
            $order = $orderItem->order;
            $discount = $order->coupon && $order->coupon->coupon_type == 'business' ? $order->discount_price : 0;
            $order->update([
                'actual_price' => $order->items()->sum('actual_price'),
                'total' => $order->items()->sum('total') + $order->delivery_fee + $order->platform_commission - $discount,
                'total_tax_price' => $order->items()->sum('tax_value'),
            ]);
        });

        /**
         * Handle the OrderItem "updated" event.
         *
         * @param  \App\Models\OrderItem  $OrderItem
         * @return void
         */
        static::updated(function (OrderItem $orderItem) {
            $order = $orderItem->order;
            $order->update([
                'actual_price' => $order->items()->sum('actual_price'),
                'total' => $order->items()->sum('total') + $order->delivery_fee + $order->platform_commission - $order->discount_price,
                'total_tax_price' => $order->items()->sum('tax_value'),
            ]);
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
