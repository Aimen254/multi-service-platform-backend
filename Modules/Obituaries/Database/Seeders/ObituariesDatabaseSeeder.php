<?php

namespace Modules\Obituaries\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ObituariesDatabaseSeeder extends Seeder
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
            ObituariesModuleTagTableSeeder::class
        ]);

        if (\env('APP_ENV') != 'production') {
            $this->call([
                ObituariesHierarchyTableSeeder::class,
                ObituariesProductsTableSeeder::class,
                ObituariesSubscriptionTableSeeder::class,
                ObituariesSubscribeTableSeeder::class
            ]);
        }
    }
}
