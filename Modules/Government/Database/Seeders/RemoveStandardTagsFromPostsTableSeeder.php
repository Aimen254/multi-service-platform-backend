<?php

namespace Modules\Government\Database\Seeders;

use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Database\Seeder;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Database\Eloquent\Model;

class RemoveStandardTagsFromPostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $government = StandardTag::where('slug', 'government')->firstOrFail();
        $posts = Product::whereRelation('standardTags', 'id', $government?->id)->get();
        foreach ($posts as $post) {
            $post->standardTags()->where('priority', 1)->detach();
            $post->status = 'tags_error';
            $post->saveQuietly();
        }
    }
}
