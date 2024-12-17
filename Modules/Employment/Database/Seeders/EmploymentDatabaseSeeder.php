<?php

namespace Modules\Employment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EmploymentDatabaseSeeder extends Seeder
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
            EmployemenModuleTableSeeder::class,
            EmploymentProductionHierarchySeederTableSeeder::class
        ]);

        if (\env('APP_ENV') != 'production') {
            $this->call([
                EmploymentHierarachyTableSeeder::class,
                JobCrafterBusinessTableSeeder::class,
                CareerCraftersIncBusinessTableSeeder::class,
                TalentHubSolutionsBusinessTableSeeder::class,
                EmploymentPostsTableSeeder::class,
                TalentHubPostsTableSeeder::class,
                CareerCrafterPostsTableSeeder::class
            ]);
        }
    }
}
