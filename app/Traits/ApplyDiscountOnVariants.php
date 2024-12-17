<?php
namespace App\Traits;
trait ApplyDiscountOnVariants
{
    /**
     * To apply discount on all variants of a product
     *
     * @param $product
     */

    public static function variantDiscount($product) {
        foreach ($product->variants as $variant) {
            if($product->discount_type == "percentage") {
                $discount = ($variant->price * $product->discount_value) / 100;
                $discount_price = $variant->price - $discount;
                $variant->update([
                    'discount_price' => $discount_price
                ]);
            } else {
                if($product->discount_value < $variant->price) {
                    $discount_price = $variant->price - $product->discount_value;
                    $variant->update([
                        'discount_price' => $discount_price
                    ]);
                }
            }
        }
    }
}