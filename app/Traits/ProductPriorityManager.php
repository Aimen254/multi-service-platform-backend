<?php

namespace App\Traits;

use App\Models\Product;
use App\Models\ProductPriority;
use DonatelloZa\RakePlus\RakePlus;

trait ProductPriorityManager
{

    public static function removePriority($productId)
    {
        ProductPriority::where('product_id', $productId)->delete();
    }
    public static function addPriority($product)
    {
            ProductTagsLevelManager::priorityOneTags($product);
            ProductTagsLevelManager::priorityTwoTags($product);
            ProductTagsLevelManager::priorityThree($product);
            ProductTagsLevelManager::priorityFour($product);
    }

    public static function updatePriorityBasedOnStatus($product)
    {
        if ($product->status === 'active') {
            self::addPriority($product);
        } else {
            self::removePriority($product->id);
        }
    }
}
