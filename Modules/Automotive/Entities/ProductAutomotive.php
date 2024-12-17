<?php

namespace Modules\Automotive\Entities;

use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAutomotive extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'maker_id',
        'model_id',
        'exterior_color_id',
        'interior_color_id',
        'body_type_id',
        'type',
        'year',
        'trim',
        'mileage',
        'vin',
        'mpg',
        'stock_no',
        'sellers_notes',
        'engine',
        'transmission',
        'drivetrain',
        'fuel_type',
    ];

    public function make()
    {
        return $this->belongsTo(StandardTag::class, 'maker_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function maker()
    {
        return $this->belongsTo(StandardTag::class, 'maker_id');
    }

    public function model()
    {
        return $this->belongsTo(StandardTag::class, 'model_id');
    }

    public function bodyType()
    {
        return $this->belongsTo(StandardTag::class, 'body_type_id');
    }
}
