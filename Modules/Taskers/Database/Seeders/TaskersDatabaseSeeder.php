<?php

namespace Modules\Taskers\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TaskersDatabaseSeeder extends Seeder
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
            TaskersModuleTagTableSeeder::class,
            TaskerHierarchyTableSeeder::class
        ]);

        if (\env('APP_ENV') != 'production') {
            $this->call([
                TaskerSubscriptionTableSeeder::class,
                TaskerCustomerTableSeeder::class,
                TaskerSubscribeTableSeeder::class,
                TaskerProductTableSeeder::class
            ]);
        }
    }
}
