<?php

namespace App\Traits;

use App\Models\Product;

trait ActiveInactiveProducts
{

    public static function activeInActiveProduct(Product $product, $previousStatus = null)
    {
        if ($product->variants->count() > 0) {
            foreach ($product->variants as $variant) {
                if ($product->status == 'active') {
                    ProductPriorityManager::addPriority($product);
                    if ($variant->previous_status == 'active') {
                        $variant->update([
                            'previous_status' => $variant->status,
                            'status' => $product->status,
                        ]);
                    }
                } else {
                    ProductPriorityManager::removePriority($product->id);
                    if ($previousStatus == 'inactive') {
                        $variant->update([
                            'status' => $product->status,
                        ]);
                    } else {
                        $variant->update([
                            'previous_status' => $variant->status,
                            'status' => $product->status,
                        ]);
                    }
                }
            }
        }
    }
}
