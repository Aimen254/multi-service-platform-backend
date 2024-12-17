<?php

namespace Modules\News\Database\Seeders;

use Carbon\Carbon;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class NewsModuleTagTableSeeder extends Seeder
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
            'name' => 'News',
            'slug' => Str::slug('News'),
            'type' => 'module',
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        StandardTag::updateOrCreate(['name' => $modulesTags['name']], [
            'slug' => $modulesTags['slug'],
            'type' => $modulesTags['type'],
            'status' => $modulesTags['status'],
            'created_at' => Carbon::now(),
        ]);
    }
}
