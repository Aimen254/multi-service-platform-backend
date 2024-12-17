<?php

namespace Modules\Blogs\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class BlogsDatabaseSeeder extends Seeder
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
            BlogsModuleTagTableSeeder::class,
            BlogsHierarchyTableSeeder::class
        ]);

        if (\env('APP_ENV') != 'production') {
            $this->call([
                BlogSubscriptionTableSeeder::class,
                BlogSubscribeTableSeeder::class,
                BlogsProductTableSeeder::class
            ]);
        }
    }
}
