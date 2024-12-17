<?php

namespace Modules\Classifieds\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ClassifiedsDatabaseSeeder extends Seeder
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
            ClassifiedsModuleTagTableSeeder::class,
            ChangeModuleTagTableSeeder::class
        ]);

        if (\env('APP_ENV') != 'production') {
            $this->call([
                ClassifiedCustomerTableSeeder::class,
                ClassifiedsHierarchyTableSeeder::class,
                ClassifiedProductTableSeeder::class,
                ClassifiedSubscriptionTableSeeder::class,
                ClassifiedSubscribeTableSeeder::class,
                ClassifiedAttributeTableSeeder::class,
            ]);
        }
    }
}
