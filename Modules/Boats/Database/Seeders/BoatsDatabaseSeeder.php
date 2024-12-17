<?php

namespace Modules\Boats\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Database\Seeders\StandardTagTableSeeder;

class BoatsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call([BoatModuleTagTableSeeder::class]);

        if (env('APP_ENV') != 'production') {
            $this->call([
                BoatsUnlimitedlaTableSeeder::class,
                BodyStylesTableSeeder::class,
                StandardTagsTableSeeder::class,
                BoatCityUsaTableSeeder::class,
                BennettBoatAndSkiTableSeeder::class,
                BoatsBusinessOwnerTableSeeder::class,
                BoatsAttributeTableSeeder::class,
                BoatsSubscriptionTableSeederTableSeeder::class,
                SubscribeBoatsTableSeeder::class
            ]);
        }
    }
}
