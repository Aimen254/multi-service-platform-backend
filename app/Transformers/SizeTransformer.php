<?php
namespace App\Transformers;

use App\Transformers\Transformer;

class SizeTransformer extends Transformer
{
    public function transform($sizes, $options = null)
    {
        $data = [
            'title' => (string) $sizes->title
        ];
        return $data;
    }
}