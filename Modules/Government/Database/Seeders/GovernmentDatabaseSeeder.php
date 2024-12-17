<?php

namespace Modules\Government\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class GovernmentDatabaseSeeder extends Seeder
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
            GovernmentModuleTableSeeder::class,
        ]);

        if (\env('APP_ENV') != 'production') {
            $this->call([
                GovernmentSubscriptionTableSeeder::class,
                GovernmentStaffTableSeeder::class,
                HierarchyTableSeeder::class,
                GovernmentDepartmentTableSeeder::class,
                HealthDepartmentTableSeeder::class,
                // DepartmentOfEducationPostsTableSeeder::class,
                HealthDepartmentPostTableSeeder::class
            ]);
        }
    }
}
