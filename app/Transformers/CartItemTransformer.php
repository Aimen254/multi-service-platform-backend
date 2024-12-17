<?php

namespace App\Transformers;

use App\Transformers\Transformer;
use stdClass;

class CartItemTransformer extends Transformer
{

    public function transform($cartItem, $options = null)
    {
        $data =  [
            'id' => (int) $cartItem->id,
            'product_variant_id' => (int) $cartItem->product_variant_id,
            'uuid' => (string) $cartItem->uuid,
            'product_id' => (int) $cartItem->product_id,
            'quantity' => (int) $cartItem->quantity,
            'unit_price' => numberFormat($cartItem->unit_price),
            'actual_price' => numberFormat($cartItem->actual_price),
            'discount_price' => $cartItem->discount_price ? numberFormat($cartItem->discount_price) : '',
            'total' => numberFormat($cartItem->total),
            'tax' => $cartItem->tax ? numberFormat($cartItem->tax) : '',
            'product_price' => numberFormat($cartItem->product_price),
            'product' => $cartItem->product ? (new ProductTransformer)->transform($cartItem->product) : new stdClass
        ];

        if ($cartItem->product_variant_id) {
            $data['variant'] = (new ProductVariantTransformer)->transform($cartItem->productVariant);
        }

        return $data;
    }
}
