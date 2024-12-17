<?php

namespace App\Transformers\Search;

use App\Transformers\Transformer;

class SearchHistoryTransformer extends Transformer
{
    public function transform($search, $options = null)
    {
        return [
            'id' => $search->id,
            'user_id' => $search->user_id,
            'keyword' => (string) $search->keyword,
            'created_at' => (string) $search->created_at
        ];
    }
}
