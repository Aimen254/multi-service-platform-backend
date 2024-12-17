<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\BusinessOwnersSeeder;
use Database\Seeders\Businesses\PerlisStoreSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // call generic seeder
        $this->call([
            RolesAndPermissionsSeeder::class,
            SettingsTableSeeder::class,
            UsersTableSeeder::class,
            LanguagesTableSeeder::class,
            DeliveryZoneTableSeeder::class,
            // env('APP_ENV') == 'production'
            //     ? ModuleTagTableSeeder::class : EmptySeeder::class,
            // env('APP_ENV') != 'production' ? HeirarchyTableSeeder::class : EmptySeeder::class,
            // env('APP_ENV') != 'production' ? AttributeTableSeeder::class : EmptySeeder::class
        ]);

        // if server not production
        if (env('APP_ENV') != 'production') {
            $this->call([
                BusinessOwnersSeeder::class
            ]);
        }
        // \App\Models\User::factory(20)->attachDetails('business_owner')->create();
        // \App\Models\User::factory(10)->attachDetails('reporter')->create();
        // \App\Models\User::factory(20)->attachDetails('driver_manager')->create();
        // \App\Models\User::factory(30)->attachDetails('driver')->create();

        $this->call([
            // BusinessTableSeeder::class,
            // PerlisStoreSeeder::class,
            // OrderStatusTableSeeder::class,
            // SubscriptionPermissionSeeder::class
        ]);
    }
}
