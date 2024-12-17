<?php
namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait BusinessOrphanTagsManager
{
    static public function addOrUpdateBusinessTags($product)
    {
        $existingTags = $product->business->extraTags()->pluck('id')->toArray();
        $productTags = $product->tags()->pluck('id')->toArray();
        $orphanTags = \array_merge($existingTags, $productTags);
        $product->business->extraTags()->sync($orphanTags);
    }
}
