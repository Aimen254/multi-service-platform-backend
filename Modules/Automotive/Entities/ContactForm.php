<?php

namespace Modules\Automotive\Entities;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'subject',
        'comment',
        'trade_in',
        'is_urgent'
    ];

    /**
     * |=====================================================================
     * | Mutators & Accessors
     * |=====================================================================
     */

    public function setIsUrgentAttribute($value)
    {
        $this->attributes['is_urgent'] = (bool) filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }


    /**
     * |=====================================================================
     * | Relationships
     * |=====================================================================
     */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
