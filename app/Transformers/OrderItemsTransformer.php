<?php

namespace App\Transformers;

use App\Transformers\Transformer;
use App\Transformers\ProductTransformer;

class OrderItemsTransformer extends Transformer
{

    public function transform($order_item, $options = null)
    {
        $productOptions = [
            'withMinimumData' => true,
            'withVariants' => (isset($options['withVariants']) && $options['withVariants']) ? true : false
        ];
        $data = [
            'id' => (int) $order_item->id,
            'product' => (new ProductTransformer)->transform($order_item->product, $productOptions),
        ];
        if (isset($options['withDetail']) && $options['withDetail']) {
            $data['quantity'] = (int) $order_item->quantity;
            $data['tax_value'] = $order_item->tax_value ? numberFormat($order_item->tax_value) : '';
            $data['refunded'] = (bool) $order_item->refunded;
            $data['total'] = numberFormat($order_item->total);
            $data['actual_price'] = numberFormat($order_item->actual_price);
            $data['discount_price'] = $order_item->discount_price ? numberFormat($order_item->discount_price) : '';
            $data['reviewed'] = $order_item->product->reviews()->where('user_id', auth()->user()->id)->first();
            $data['size'] = $order_item->size;
            $data['color'] = $order_item->color;
        }

        if ($order_item->product_variant_id) {
            $data['variant'] = (new ProductVariantTransformer)->transform($order_item->productVariant);
        }

        return $data;
    }
}
