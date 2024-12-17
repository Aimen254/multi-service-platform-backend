<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Retail\Entities\DeliveryZone;

class DeliveryZoneTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::whereUserType('newspaper')->first();
        DeliveryZone::create([
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'zone_type' => 'circle',
            'fee_type' => 'Delivery fee by mileage',
            'platform_delivery_type' => 'standard_retail',
        ]);
    }
}
