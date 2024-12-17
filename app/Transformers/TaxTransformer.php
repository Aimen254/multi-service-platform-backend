<?php
namespace App\Transformers;

class TaxTransformer extends Transformer
{
    public function transform($product, $options = null): array
    {
        return [
            'id' => $product->id,
            'tax_percentage' => (string) $product->tax_percentage ?? '',
        ];
    }
}
