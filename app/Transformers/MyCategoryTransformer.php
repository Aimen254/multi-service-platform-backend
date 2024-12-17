<?php

namespace App\Transformers;

use App\Transformers\Transformer;

class MyCategoryTransformer extends Transformer
{
    public function transform($category, $options = null)
    {
        $data = [
            'id' => (int) $category?->id,
            'from' => (string) $category?->from,
            'to' => (string) $category?->to,
            'min_price' => $category?->min_price,
            'max_price' => $category?->max_price,
            'module' => $category?->module
                ? (new StandardTagTransformer)->transform($category?->module) : null,
            'maker' => $category?->maker
                ? (new StandardTagTransformer)->transform($category?->maker) : null,
            'model' => $category?->model
                ? (new StandardTagTransformer)->transform($category?->model) : null,
            'level_four_tag' => $category?->level_four_tag
                ? (new StandardTagTransformer)->transform($category->level_four_tag) : null
        ];
        return $data;
    }
}
