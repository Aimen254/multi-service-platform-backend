<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\Business;

trait BusinessSettings
{
    /**
     * To create settings of business.
     *
     * @param $business
     */
    public static function addBusinessSettings(Business $business)
    {
        $business->settings()->insert([
            [
                'business_id' => $business->id,
                'key' => 'minimum_purchase',
                'value' => $business->minimum_purchase,
                'created_at' => Carbon::now()
            ],
            [
                'business_id' => $business->id,
                'key' => 'tax_apply',
                'value' => 0,
                'created_at' => Carbon::now()
            ],
            [
                'business_id' => $business->id,
                'key' => 'tax_percentage',
                'value' => 0,
                'created_at' => Carbon::now()
            ],
            [
                'business_id' => $business->id,
                'key' => 'global_price',
                'value' => 0.00,
                'created_at' => Carbon::now()
            ],
            [
                'business_id' => $business->id,
                'key' => 'custom_platform_fee_type',
                'value' => 0,
                'created_at' => Carbon::now()
            ],
            [
                'business_id' => $business->id,
                'key' => 'custom_platform_fee_value',
                'value' => 0,
                'created_at' => Carbon::now()
            ],
            [
                'business_id' => $business->id,
                'key' => 'delivery_time',
                'value' => 0,
                'created_at' => Carbon::now()
            ],
            [
                'business_id' => $business->id,
                'key' => 'pickup_time',
                'value' => 0,
                'created_at' => Carbon::now()
            ],
            [
                'business_id' => $business->id,
                'key' => 'deliverable',
                'value' => 1,
                'created_at' => Carbon::now()
            ],
        ]);
    }

    /**
     * To create schedule of business.
     *
     * @param $business
     */
    public static function addBusinessSchedule(Business $business)
    {
        $business->businessschedules()->insert([
            [
                'business_id' => $business->id,
                'name' => 'Sunday',
                'status' => 'inactive',
                'created_at' => Carbon::now()
            ],
            [
                'business_id' => $business->id,
                'name' => 'Monday',
                'status' => 'inactive',
                'created_at' => Carbon::now()
            ],
            [
                'business_id' => $business->id,
                'name' => 'Tuesday',
                'status' => 'inactive',
                'created_at' => Carbon::now()
            ],
            [
                'business_id' => $business->id,
                'name' => 'Wednesday',
                'status' => 'inactive',
                'created_at' => Carbon::now()
            ],
            [
                'business_id' => $business->id,
                'name' => 'Thursday',
                'status' => 'inactive',
                'created_at' => Carbon::now()
            ],
            [
                'business_id' => $business->id,
                'name' => 'Friday',
                'status' => 'inactive',
                'created_at' => Carbon::now()
            ],
            [
                'business_id' => $business->id,
                'name' => 'Saturday',
                'status' => 'inactive',
                'created_at' => Carbon::now()
            ],
        ]);
    }

    /**
     * To create delivery settings of business.
     *
     * @param $business
     */
    public static function addBusinessDeliverySetting(Business $business)
    {
        $business->deliveryZone()->create(['delivery_type' => 'NoDelivery']);
    }
}
