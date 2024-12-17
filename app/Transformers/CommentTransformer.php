<?php

namespace App\Transformers;

use stdClass;
use App\Transformers\Transformer;
use App\Transformers\UserTransformer;

class CommentTransformer extends Transformer
{
    public function transform($comment, $options = null)
    {

        $data = [
            'id' => (int) $comment->id,
            'product_id' => (string) $comment->model_id,
            'comment' => (string) $comment->comment,
            'user' => $comment->relationLoaded('user') && $comment->user ? (new UserTransformer)->transform($comment->user) : new stdClass(),
            'created_at' => convertDate($comment->created_at, 'M d, Y')
        ];

        return $data;
    }
}
