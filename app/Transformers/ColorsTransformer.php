<?php
namespace App\Transformers;

use App\Transformers\Transformer;

class ColorsTransformer extends Transformer
{
    public function transform($colors, $options = null)
    {
        $data = [
            'title' => (string) $colors->title
        ];
        return $data;
    }
}