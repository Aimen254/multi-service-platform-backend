<?php

namespace Modules\Automotive\Entities;

use Stringable;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Database\Eloquent\Model;
use App\Jobs\SyncMyCategoryProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DreamCar extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'make_id',
        'model_id',
        'user_id',
        'level_four_tag_id',
        'from',
        'to',
        'min_price',
        'max_price',
        'bed',
        'bath',
        'square_feet'
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
         * Handle the dream car "created" event.
         *
         * @return void
         */
        static::created(function (DreamCar $dreamCar) {
            SyncMyCategoryProduct::dispatch(request()->input('module'), $dreamCar);
        });       


        static::updated(function (DreamCar $dreamCar) {
            SyncMyCategoryProduct::dispatch(request()->input('module'), $dreamCar);
        });

        static::deleting(function (DreamCar $dreamCar) {
            $dreamCar->products()->detach();
        });
        /**
         * Handle the dream car "updated" event.
         *
         * @return void
         */
    }

    public function module()
    {
        return $this->belongsTo(StandardTag::class, 'module_id');
    }

    public function maker()
    {
        return $this->belongsTo(StandardTag::class, 'make_id');
    }

    public function model()
    {
        return $this->belongsTo(StandardTag::class, 'model_id');
    }
    public function level_four_tag()
    {
        return $this->belongsTo(StandardTag::class, 'level_four_tag_id');
    }
    public function beds()
    {
        return $this->belongsTo(StandardTag::class, 'bed');
    }
    public function baths()
    {
        return $this->belongsTo(StandardTag::class, 'bath');
    }
    public function squareFeets()
    {
        return $this->belongsTo(StandardTag::class, 'square_feet');
    }

    // public function products()
    // {
    //     return $this->belongsToMany(Product::class, 'product_dream_car');
    // }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'dream_car_product', 'dream_car_id', 'product_id');
    }
}
