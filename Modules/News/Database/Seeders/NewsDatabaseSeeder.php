<?php

namespace Modules\News\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class NewsDatabaseSeeder extends Seeder
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
            NewsModuleTagTableSeeder::class,
        ]);

        if (\env('APP_ENV') != 'production') {
            $this->call([
                NewsHierarchyTableSeeder::class,
                NewsSubscriptionTableSeeder::class,
                NewsReporterSeederTableSeeder::class,
                NewsSubscribeTableSeeder::class
            ]);
        } else {
            $this->call(ProductionHierarchySeederTableSeeder::class);
        }
    }
}
