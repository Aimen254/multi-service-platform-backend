<?php
namespace App\Transformers;

class DiscountTransformer extends Transformer
{
    public function transform($product, $options = null): array
    {
        return [
            'id' => $product->id,
            'discount_type' => (string) $product->discount_type ?? '',
            'discount_price' => (string) $product->discount_price ?? '',
            'discount_start_date' => (string) $product->discount_start_date ?? '',
            'discount_end_date' => (string) $product->discount_end_date ?? '',
            'discount_value' => (string) $product->discount_value ?? '',
        ];
    }
}
