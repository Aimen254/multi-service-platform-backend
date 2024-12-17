<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class ModuleTagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modulesTags = [
            [
                'name' => 'Retail',
                'slug' => Str::slug('Retail'),
                'type' => 'module',
                'status' => 'active',
                'priority' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        StandardTag::insert($modulesTags);
    }
}
