<?php

namespace Modules\Boats\Database\Seeders;

use App\Models\StandardTag;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use PhpParser\PrettyPrinter\Standard;

class StandardTagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $levelTwostandardTags = [
            [
                'name' => 'Manufacturers',
                'slug' => Str::slug('Manufacturers'),
                'type' => 'product',
                'priority' => 1,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Body Types',
                'slug' => Str::slug('Body Types'),
                'type' => 'product',
                'priority' => 1,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($levelTwostandardTags as $tag) {
            StandardTag::updateOrCreate(['name' => $tag['name']], [
                'slug' => $tag['slug'],
                'type' => $tag['type'],
                'priority' => $tag['priority'],
                'status' => $tag['status'],
                'created_at' => $tag['created_at'],
                'updated_at' => $tag['updated_at'],
            ]);
        }
    }
}
