<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\Business;
use Illuminate\Database\Eloquent\Model;
use App\Enums\OrderStatus as OrderStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'order_id',
        'uuid',
        'model_id',
        'recepient_id',
        'model_type',
        'business_id',
        'coupon_id',
        'total',
        'note',
        'shipping_date',
        'order_status_id',
        'order_type',
        'shipping_id',
        'billing_id',
        'total_tax_price',
        'actual_price',
        'discount_price',
        'discount_value',
        'discount_type',
        'delivery_fee',
        'mailing_id',
        'tax_type',
        'charged',
        'stripe_decline_code',
        'stripe_error_code',
        'stripe_message',
        'selected_card',
        'payment_intent_id',
        'refunded',
        'captured',
        'platform_fee_type',
        'platform_fee_value',
        'platform_commission',
        'delivery_owner',
        'amount_refunded',
        'rejection_reason',
        'refunded_delivery_fee',
        'refunded_platform_fee',
        'stripe_platform_id'
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
         * Handle the product "creating" event.
         *
         * @return void
         */
        static::creating(function (Order $order) {
            $order->uuid = Str::uuid();
        });

        /**
         * Handle the product "created" event.
         *
         * @return void
         */
        static::created(function (Order $order) {
            // creating order tracking entry
            OrderTracking::create(['order_id' => $order->id, 'order_status_id' => $order->order_status_id]);
            // updated order by uzair
            $order->update(['order_id' => '#' . str_pad($order->id + 1, 4, "100", STR_PAD_LEFT)]);
        });

        /**
         * Handle the product "updated" event.
         *
         * @return void
         */
        static::updated(function (Order $order) {
            $OrderTracking = OrderTracking::where('order_id', $order->id)->where('order_status_id', $order->order_status_id)->exists();
            if (!$OrderTracking && $order->order_status_id != OrderStatusEnum::Processing) {
                // creating order tracking entry
                OrderTracking::create(['order_id' => $order->id, 'order_status_id' => $order->order_status_id]);
            }
        });
    }

    public function status(): Attribute
    {
        return new Attribute(
            get: fn ($value) => OrderStatus::fromValue((int)$value)->description,
            set: fn ($value) => OrderStatus::coerce(str_replace(' ', '', ucwords($value)))->value
        );
    }

    public function model()
    {
        return $this->morphTo()->withTrashed();
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_id');
    }

    public function billingAddress()
    {
        return $this->belongsTo(Address::class, 'billing_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function orderTracking()
    {
        return $this->hasMany(OrderTracking::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }
    public function recepient()
    {
        return $this->belongsTo(Recepient::class, 'recepient_id');
    }
}
