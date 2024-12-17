<?php
namespace App\Transformers\Search;

use App\Transformers\Transformer;

class SearchBusinessTransformer extends Transformer
{
    public function transform($business, $options = null)
    {
        return [
            'id' => $business->id,
            'uuid' => (string) $business->uuid,
            'name' => (string) $business->name,
            'slug' => $business->slug,
            'reviews_avg' => (float) $business->reviews_avg,
            'banner' => $business->banner
                ? getImage($business->banner->path, 'image', $business->banner->is_external)
                : getImage(NULL, 'image'),
        ];
    }
}