<?php

namespace App\Transformers\Search;

use App\Models\Product;
use App\Models\StandardTag;
use App\Transformers\Transformer;

class SearchTransformer extends Transformer
{
    public function transform($search, $options = null)
    {
        $data = [
            'L1' => [
                'id' => $search?->level_one_id,
                'slug' => $search?->level_one_slug,
                'name' => $search?->level_one_name,
            ],
            'L2' => [
                'id' => $search?->level_two_id,
                'slug' => $search?->level_two_slug,
                'name' => $search?->level_two_name,
            ],
            'L3' => [
                'id' => $search?->level_three_id,
                'slug' => $search?->level_three_slug,
                'name' => $search?->level_three_name,
            ],
            'L4' => [
                'id' => $search?->level_four_id,
                'slug' => $search?->level_four_slug,
                'name' => $search?->level_four_name,
            ],
            // 'products_count' => $search->products_count,
            'total_relevancy' => $search->word_count
        ];

        $products = $this->getProduts($search, $options['searchString']);
        $data['products'] = (new SearchProductTransformer)->transformCollection($products, [
            'L1' => $data['L1'], 'L2' => $data['L2'], 'L3' => $data['L3']
        ]);

        return $data;
    }

    private function getProduts($search, $searchString)
    {
        $products = Product::active()->matchAgainstWithPriority($searchString)
            ->hierarchyBasedProducts($search)->take(4)->get();
        return $products;
    }
}
