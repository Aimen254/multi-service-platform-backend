<?php

namespace Modules\Retail\Entities;

use App\Models\Business;
use App\Enums\PlatfromFeeType;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Business\Settings\TaxType;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['business_id', 'title', 'key', 'value', 'status'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::boot();

        /**
         * Handle the Business Setting "updated" event.
         *
         * @param  \App\Models\Business  $business
         * @return void
         */
        static::updated(function (BusinessSetting $businessSetting) {
            if ($businessSetting->key == 'minimum_purchase') {
                $businessSetting->business()->update(['minimum_purchase' => $businessSetting->value]);
            }
        });
    }

    public function getValueAttribute($value)
    {
        switch ($this->key) {
            case 'tax_type':
                $value = TaxType::fromValue((int)$value)->description;
                break;
            case 'custom_platform_fee_type':
                $value = PlatfromFeeType::fromValue((int)$value)->description;
                break;
        }
        return $value;
    }

    public function setValueAttribute($value)
    {
        switch ($this->key) {
            case 'tax_type':
                $value = TaxType::coerce(str_replace(' ', '', ucwords($value)))->value;
                break;
            case 'custom_platform_fee_type':
                $value = PlatfromFeeType::coerce(str_replace(' ', '', ucwords($value)))->value;
                break;
        }
        $this->attributes['value'] = $value;
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
