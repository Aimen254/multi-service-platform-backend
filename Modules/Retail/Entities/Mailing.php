<?php

namespace Modules\Retail\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mailing extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    public function getPriceAttribute($value)
    {
        return number_format((float) $value, 2, '.', '');
    }

    public function getMinimumAmountAttribute($value)
    {
        return number_format((float) $value, 2, '.', '');
    }
    protected $fillable = ['business_id', 'title', 'minimum_amount', 'price', 'status'];

    public function statusChanger()
    {
        $this->status = $this->status == 'active' ? 'inactive' : 'active';
        return $this;
    }
}
