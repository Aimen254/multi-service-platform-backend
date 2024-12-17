<?php

namespace Modules\News\Database\Seeders;

use App\Models\Product;
use App\Models\StandardTag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class NewsItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $reporter1 = User::whereEmail('reporter01@interapptive.com')->first();
        $reporter2 = User::whereEmail('reporter02@interapptive.com')->first();

        // Shuffle the reporters to assign them randomly
        $reporterIds = [$reporter1->id, $reporter2->id];

        $news = Product::whereRelation('standardTags', 'slug', 'news')->get();
        foreach ($news as $item) {
            // Get a random reporter from the shuffled array
            $randomOwnerId = $reporterIds[array_rand($reporterIds)];
            $item->user_id = $randomOwnerId;
            $item->saveQuietly();
        }
    }
}
