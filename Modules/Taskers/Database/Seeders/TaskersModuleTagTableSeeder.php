<?php

namespace Modules\Taskers\Database\Seeders;

use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TaskersModuleTagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $modulesTag = [
            'name' => 'Taskers',
            'slug' => Str::slug('Taskers'),
            'priority' => 1,
            'type' => 'module',
            'status' => 'active'
        ];

        StandardTag::updateOrCreate(['name' => $modulesTag['name']], $modulesTag);
    }
}
