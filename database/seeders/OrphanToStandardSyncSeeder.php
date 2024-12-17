<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class OrphanToStandardSyncSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orphanTags = Tag::where('mapped_to', '!=', null)->get();
        if (count($orphanTags) > 0) {
            $orphanTags->each(function ($tag, $key) {
                $tag->standardTags_()->syncWithoutDetaching($tag->mapped_to);
            });
        }
    }
}
