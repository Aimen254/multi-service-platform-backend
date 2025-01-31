<?php

namespace Modules\Posts\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class PostsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");
        $this->call([
            PostModuleTagTableSeeder::class,
            PostsHierarchyTableSeeder::class,
            PostGenerateTableSeeder::class,
            PostsSubscriptionTableSeeder::class,
            PostsSubscribeTableSeeder::class
        ]);
    }
}
