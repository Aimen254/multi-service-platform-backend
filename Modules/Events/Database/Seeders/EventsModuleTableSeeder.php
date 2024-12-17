<?php

namespace Modules\Events\Database\Seeders;

use Carbon\Carbon;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EventsModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $modulesTags = [
            'name' => 'Events',
            'slug' => Str::slug('Events'),
            'type' => 'module',
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        StandardTag::updateOrCreate(['name' => $modulesTags['name']], [
            'slug' => $modulesTags['slug'],
            'type' => $modulesTags['type'],
            'priority' => 1,
            'status' => $modulesTags['status'],
            'created_at' => Carbon::now(),
        ]);
    }
}
