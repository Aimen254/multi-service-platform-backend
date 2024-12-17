<?php

namespace Modules\Events\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EventsDatabaseSeeder extends Seeder
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
            EventsModuleTableSeeder::class,
            ProductionHierarchySeederTableSeeder::class
        ]);

        if (\env('APP_ENV') != 'production') {
            $this->call([
                EventsSubscriptionTableSeeder::class,
                EventSubscribeTableSeeder::class,
                EventsGenerateTableSeeder::class
            ]);
        }
    }
}
