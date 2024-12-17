<?php

namespace Modules\Retail\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Enums\Business\Settings\DeliveryType;
use App\Enums\Business\Settings\CustomDeliveryFee;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryZone extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'model_id',
        'model_type',
        'zone_type',
        'delivery_type',
        'mileage_fee',
        'extra_mileage_fee',
        'mileage_distance',
        'fee_type',
        'fixed_amount',
        'percentage_amount',
        'latitude',
        'longitude',
        'radius',
        'polygon',
        'address',
        'status',
        'platform_delivery_type'
    ];

    // 'zone_type' => $request->input('zone_type') ? $request->input('zone_type') : null ,
    //             'latitude' => $center ? $center['lat'] : null ,
    //             'longitude' => $center ? $center['lng'] : null ,
    //             'radius' => $request->input('radius') ? $request->radius : null ,
    //             'polygon' => $request->input('polygon')  ? json_encode($request->polygon) : null ,
    //             'address' => $request->input('address') ? $request->input('address') : null ,
    

    public function getDeliveryTypeAttribute($value)
    {
        return DeliveryType::fromValue((int)$value)->description;
    }

    public function setDeliveryTypeAttribute($value)
    {
        $this->attributes['delivery_type'] = DeliveryType::coerce(str_replace(' ', '', ucwords($value)))->value;
    }

    public function getFeeTypeAttribute($value)
    {
        return isset($value) ? CustomDeliveryFee::fromValue((int)$value)->description : null;
    }

    public function setFeeTypeAttribute($value)
    {
        if ($value) {
            $this->attributes['fee_type'] = CustomDeliveryFee::coerce(str_replace(' ', '', ucwords($value)))->value;
        }
    }

    /**
     * Get the parent model
     */
    public function model()
    {
        return $this->morphTo();
    }


    public function statusChanger()
    {
        $this->status = $this->status == 'active' ? 'inactive' : 'active';
        return $this;
    }
}
