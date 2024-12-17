<?php

namespace Modules\Retail\Entities;

use App\Models\Size;
use App\Models\Color;
use App\Models\Media;
use App\Models\Product;
use App\Traits\ApplyDiscountOnVariants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $casts = [
        'tags_data' => 'array',
    ];
    protected $fillable = [
        'product_id',
        'color_id',
        'size_id',
        'price',
        'title',
        'sku',
        'price',
        'quantity',
        'custom_size',
        'custom_color',
        'previous_status',
        'status',
        'stock_status',
        'discount_price',
        'external_id',
        'tags_data'
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
         * Handle the Business "created" event.
         *
         * @param  \App\Models\Business  $business
         * @return void
         */
        static::created(function (ProductVariant $variant) {
            $product = $variant->product;

            $variantCount = $product->variants()->count();
            if ($variant->product->sku != null  && $variant->sku == null) {
                $sku = $variant->product->sku . '-' . ($variantCount);
                $variant->update(['sku' => $sku]);
            }
            if ($product->discount_value) {
                ApplyDiscountOnVariants::variantDiscount($product);
            }
        });


        /**
         * Handle the variant "updated" event.
         *
         * @param  \App\Models\Business  $business
         * @return void
         */
        static::updated(function (ProductVariant $variant) {
            $product = $variant->product;

            $variantCount = $product->variants()->count();
            if ($variant->product->sku != null  && $variant->sku == null) {
                $sku = $variant->product->sku . '-' . ($variantCount);
                $variant->update(['sku' => $sku]);
            }
            if ($product->discount_value) {
                ApplyDiscountOnVariants::variantDiscount($product);
            }
        });

        /**
         * Handle the Business "deleting" event.
         *
         * @param  \App\Models\Business  $business
         * @return void
         */
        static::deleting(function (ProductVariant $variant) {
            if ($variant->image) {
                deleteFile($variant->image->path);
                $variant->image()->delete();
            }
        });
    }

    public function scopeActiveAndInStock($query)
    {
        return $query->where('status', 'active')
                     ->where('stock_status', 'in_stock')
                     ->where(function ($query) {
                         $query->where('quantity', '>', 0)
                               ->orWhere('quantity', '=', -1);
                     });
    }
    
    public function statusChanger()
    {
        $this->status = $this->status == 'active' ? 'inactive' : 'active';
        return $this;
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function image()
    {
        return $this->morphOne(Media::class, 'model')->where('type', 'image');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
