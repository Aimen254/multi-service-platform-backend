<?php

namespace App\Transformers;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Transformers\Transformer;
use Illuminate\Support\Facades\Log;

class ProductVariantTransformer extends Transformer
{
    public function transform($productVariant, $options = null)
    {
        // Eager load related models
        $productVariant->load('color', 'size');

        $date = Carbon::now()->format('Y-m-d');
        $discount_start_date = $productVariant->product->discount_start_date ? $productVariant->product->discount_start_date->format('Y-m-d') : '';
        $discount_end_date = $productVariant->product->discount_end_date ? $productVariant->product->discount_end_date->format('Y-m-d') : '';

        if (isset($options['withMinimumData']) && Arr::exists($options, 'withMinimumData') && $options['withMinimumData']) {
            return [
                'color' => $productVariant->color ? $productVariant->color->title : '',
                'custom_color' => (string) $productVariant->custom_color,
            ];
        } else {
            return [
                'id' =>  $productVariant->id,
                'title' =>  $productVariant->title,
                'sku' => (string) $productVariant->sku,
                'price' =>  numberFormat($productVariant->price),
                'discount_price' => $productVariant->discount_price &&  $discount_start_date <= $date &&  $discount_end_date >= $date
                ? numberFormat($productVariant->discount_price) : '',
                'quantity' => (int) $productVariant->quantity,
                'status' => (string) $productVariant->status,
                'stock_status' => (string) $productVariant->stock_status,
                'color' => $productVariant->color ? $productVariant->color->title : '',
                'size' => $productVariant->size ? $productVariant->size->title : '',
                'color_id' => (int) $productVariant->color_id,
                'size_id' => (int) $productVariant->size_id,
                'custom_color' => (string) $productVariant->custom_color,
                'custom_size' => (string) $productVariant->custom_size,
                'image' => getImageUrl($productVariant->image),
            ];
        }
    }
}
