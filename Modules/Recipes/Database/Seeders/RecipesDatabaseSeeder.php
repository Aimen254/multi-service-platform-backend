<?php

namespace Modules\Recipes\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecipesDatabaseSeeder extends Seeder
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
            RecipesModuleTagTableSeeder::class
        ]);

        if (\env('APP_ENV') != 'production') {
            $this->call([
                RecipesHierarchyTableSeeder::class,
                RecipeProductsTableSeeder::class,
                RecipesSubscriptionSeederTableSeeder::class,
                RecipesSubscribeTableSeeder::class,
            ]);
        }
    }
}
