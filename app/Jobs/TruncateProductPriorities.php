<?php

namespace App\Jobs;

use App\Models\StandardTag;
use Illuminate\Bus\Queueable;
use App\Models\ProductPriority;
use App\Traits\ProductTitleTags;
use Illuminate\Queue\SerializesModels;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class TruncateProductPriorities implements ShouldQueue
{
    // implements ShouldQueue
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ProductPriority::truncate();
        $moduleTags = StandardTag::with(['productTags' => function($query) {
            $query->active();
        }])->whereIn('slug', ['blogs', 'news', 'recipes', 'events', 'employment', 'automotive', 'boats', 'government', 'marketplace', 'obituaries', 'services', 'real-estate', 'taskers', 'notices'])->get();
        foreach ($moduleTags as $moduleTag) {
            $products = $moduleTag->productTags ?? [];
            foreach ($products as $product) {
                if ($product->status === 'active') {
                    // ProductTitleTags::createTag($product);
                    ProductTagsLevelManager::priorityOneTags($product);
                    ProductTagsLevelManager::priorityTwoTags($product);
                    ProductTagsLevelManager::priorityThree($product);
                    ProductTagsLevelManager::priorityFour($product);
                }
            }
        }
    }
}
