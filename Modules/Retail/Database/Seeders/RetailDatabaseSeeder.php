<?php

namespace Modules\Retail\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RetailDatabaseSeeder extends Seeder
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
            RetailModuleTagSeederTableSeeder::class,
            RetailHierarchyTagsTableSeeder::class
        ]);

        if (\env('APP_ENV') != 'production') {
            $this->call([
                PerlisTableSeeder::class,
                SubscriptionPermissionTableSeeder::class,
                FrockCandyTableSeeder::class,
                CarriagesFineClothierTableSeeder::class,
                HKyleBoutiqueTableSeeder::class,
                MessengersGiftsTableSeeder::class,
                VictoriasToysStationTableSeeder::class,
                TheKeepingRoomTableSeeder::class,
                RetailBusinessOwnersUpdateTableSeeder::class,
            ]);
        }
    }
}
