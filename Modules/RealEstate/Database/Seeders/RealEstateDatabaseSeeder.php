<?php

namespace Modules\RealEstate\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RealEstateDatabaseSeeder extends Seeder
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
            RealEstateModuleTagTableSeeder::class,
            ProductionHierarchySeederTableSeeder::class
        ]);

        if (\env('APP_ENV') != 'production') {
            $this->call([
                RealEstateHierarchyTableSeeder::class,
                DreamEstateBrokerTableSeeder::class,
                PioneerPropertyPartnersTableSeeder::class,
                RealEstateAgentTableSeeder::class,
                PropertyListingSeederTableSeeder::class,
                RealEstateSubscriptionTableSeeder::class,
                SubscribeRealEstateTableSeeder::class,
                UpdateStandardTagTableSeeder::class
            ]);
        }
    }
}
