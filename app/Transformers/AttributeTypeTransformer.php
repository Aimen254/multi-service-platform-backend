<?php
namespace App\Transformers;

use App\Transformers\Transformer;

class AttributeTypeTransformer extends Transformer
{
    public function transform($attributeType, $options = null)
    {
        $data = [
            'id' => (int) $attributeType->id,
            'name' => (string) $attributeType->name,
        ];
        return $data;
    }
}