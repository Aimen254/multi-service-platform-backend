<?php

namespace Modules\Classifieds\Database\Seeders;

use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ChangeModuleTagTableSeeder extends Seeder
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
            'name' => 'Marketplace',
            'slug' => Str::slug('Marketplace'),
            'type' => 'module',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        StandardTag::where('name', 'Classifieds')->where('type', 'module')->update($modulesTags);
    }
}
