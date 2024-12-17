<?php

namespace Modules\Notices\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class NoticesDatabaseSeeder extends Seeder
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
            NoticesModuleSeederTableSeeder::class,
            NoticesHierarchyTableSeeder::class
        ]);

        if (\env('APP_ENV') != 'production') {
            $this->call([
                NoticesSubscriptionTableSeeder::class,
                SubscribeNoticesTableSeeder::class,
                OrganizationTableSeeder::class,
                NoticesTableSeeder::class
            ]);
        }
    }
}
