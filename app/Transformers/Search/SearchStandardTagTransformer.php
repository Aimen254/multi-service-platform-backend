<?php
namespace App\Transformers\Search;

use App\Transformers\Transformer;

class SearchStandardTagTransformer extends Transformer
{
    public function transform($standardTag, $options = null)
    {
        return [
            'id' => (int) $standardTag->id,
            'name' => (string) $standardTag->name,
            'slug' => (string) $standardTag->slug,
        ];
    }
}