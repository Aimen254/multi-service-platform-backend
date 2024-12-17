<?php

namespace Modules\Government\Database\Seeders;

use Carbon\Carbon;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class GovernmentModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Model::unguard();

        $modulesTags = [
            'name' => 'Government',
            'slug' => Str::slug('Government'),
            'type' => 'module',
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        StandardTag::updateOrCreate(['name' => $modulesTags['name']], [
            'slug' => $modulesTags['slug'],
            'type' => $modulesTags['type'],
            'status' => $modulesTags['status'],
            'priority' => 1,
            'created_at' => Carbon::now(),
        ]);
    }
}
