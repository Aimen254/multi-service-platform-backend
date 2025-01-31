<?php

namespace Modules\Services\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ServicesDatabaseSeeder extends Seeder
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
            ServicesModuleTagTableSeeder::class,
            AbbBusinessTableSeeder::class,
            ServicesBusinessTableSeeder::class,
            HierarchyTableSeeder::class,
            ServicesProductSeederTableSeeder::class,
            AbbServicesTableSeeder::class,
            ServiceSubscriptionTableSeeder::class,
            SubscribeServiceTableSeeder::class

        ]);
    }
}
