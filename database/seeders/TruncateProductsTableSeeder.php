<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TruncateProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tagIds = Tag::whereDoesntHave('standardTags_')->pluck('id')->toArray();
        $businesses = Business::whereHas('tags', function ($query) use ($tagIds) {
            $query->whereIn('id', $tagIds);
        })->get();
        foreach ($businesses as $business) {
            $business->tags()->detach($tagIds);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('contact_forms')->truncate();
        DB::table('products')->truncate();
        DB::table('product_tag')->truncate();
        DB::table('product_standard_tag')->truncate();
        Tag::whereDoesntHave('standardTags_')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
