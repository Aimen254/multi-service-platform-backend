<?php

namespace Modules\Automotive\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class AutomotiveDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call([
            AutomotiveModuleTagTableSeeder::class,
            YearMakeModelTrimByTeoalidaTableSeeder::class,
        ]);

        if (env('APP_ENV') != 'production') {
            $this->call([
                MotorCarsSeederTableSeeder::class,
                InteriorColorTableSeeder::class,
                ExteriorColorTableSeeder::class,
                AutomotiveAttributeTableSeeder::class,
                YearAttributeTableSeeder::class,
                AutomotiveBusinessOwnerTableSeeder::class,
                ChangeBodyStyleTypeTableSeeder::class,
                AutomotiveSubscriptionTableSeeder::class,
                SubscribeAutomotiveTableSeeder::class,
                AutomotiveConversationSeederTableSeeder::class
            ]);
        }
    }
}
