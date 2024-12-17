<?php

namespace App\Transformers\MyLv;

use Carbon\Carbon;
use App\Models\StandardTag;
use App\Transformers\Transformer;
use App\Transformers\MyLv\AuthorTransformer;

class ProductTransformer extends Transformer
{
    function transform($product, $options = null)
    {
        $levelOne = $product->standardTags()->where('type', 'module')->first();
        $date = Carbon::now()->format('Y-m-d');
        $discount_start_date = $product?->discount_start_date ? $product?->discount_start_date->format('Y-m-d') : '';
        $discount_end_date = $product?->discount_end_date ? $product?->discount_end_date->format('Y-m-d') : '';
        $levelTwo = $product->standardTags()->whereHas('levelTwo', function ($query) use ($levelOne) {
            $query->where('L1', $levelOne->id);
        })
            ->first();
        if ($levelTwo) {
            $levelThree = $product->standardTags()->whereHas('levelThree', function ($query) use ($levelOne, $levelTwo) {
                $query->where('L1', $levelOne->id)->where('L2', $levelTwo->id);
            })->first();
             $levelFour=null;
            if ($levelThree) {
                $levelFour = StandardTag::whereHas('productTags', function ($query) use ($levelOne, $levelTwo, $levelThree, $product) {
                    $query->where('product_id', $product->id)->whereHas('standardTags', function ($subQuery) use ($levelOne, $levelTwo, $levelThree) {
                        if ($levelOne && $levelTwo && $levelThree) {
                            $subQuery->whereIn('id', [$levelOne->id, $levelTwo->id, $levelThree->id]);
                        }
                    });
                })->whereHas('tagHierarchies', function ($query) use ($levelOne, $levelTwo, $levelThree) {
                    $query->where('L1', $levelOne->id)->where('L2', $levelTwo->id)->where('L3', $levelThree->id);
                    $query->where('level_type', 4);
                })->first();
            }
        }
        $data = [
            'id' => (int)$product->id,
            'uuid' => (string)$product->uuid,
            'name' => (string) $product->name ? $product->name : $product->description,
            'discount_price' => $product?->discount_price && $discount_start_date <= $date && $discount_end_date >= $date ? numberFormat($product?->discount_price) : '',
            'discount_type' => $product->discount_type ?  $product->discount_type : '',
            'discount_value' => $product->discount_value ?  $product->discount_value : '',
            'main_image' => $product->mainImage
                ? getImage($product?->mainImage?->path, 'image', $product->mainImage->is_external)
                : (in_array($levelOne?->slug, ['government']) ? '' : getImage(NULL, 'image')),
            'price' => $product?->price ? getPriceFormat($product->price, $levelOne?->slug) : null,
            'is_business' => (bool)$product->business_id ? \true : \false,
            'created_at' => Carbon::parse($product->product_created_at)->format('d M, Y'),
            'product_description' => $product->description,
            'author' =>  [
                'id' => $product->business_id ? $product->business_id : $product?->user_id,
                'name' => $product?->business_id ? $product->business?->name : $product->user?->first_name . ' ' . $product->user?->last_name,
                'uuid' => $product?->business?->uuid,
                'slug' => $product?->business?->slug,
                'logo' => $product->business_id
                    ? (
                        $product->business?->logo
                            ? getImage($product?->business?->logo->path, 'image', $product?->business?->logo->is_external)
                            : getImage(NULL, 'image')
                    ) : (getImage($product->user?->avatar, 'avatar', 0))
            ],

            'L1' => [
                'id' => $levelOne?->id,
                'name' => $levelOne?->name,
                'slug' => $levelOne?->slug
            ],
            'L2' => [
                'id' => $levelTwo?->id,
                'name' => $levelTwo?->name,
                'slug' => $levelTwo?->slug
            ],
            'L3' => [
                'id' => $levelThree?->id,
                'name' => $levelThree?->name,
                'slug' => $levelThree?->slug
            ],
            'L4' => [
                'id' => $levelFour?->id,
                'name' => $levelFour?->name,
                'slug' => $levelFour?->slug
            ]
        ];
        return $data;
    }

    private function getUniqueLevels($product, $level)
    {
        // Convert the relations to collections
        $productLevelOne = collect($product->productLevelOne);
        $productLevelTwo = collect($product->productLevelTwo);
        $productLevelThree = collect($product->productLevelThree);
        $productLevelFour = collect($product->productLevelFour);
        if ($level == 2) {
            $tags = $productLevelTwo->reject(function ($levelOne) use ($productLevelOne) {
                return $productLevelOne->contains('id', $levelOne['id']);
            })->values();
            return $tags;
        } else if ($level == 3) {
            $tags = $productLevelThree->reject(function ($levelOne) use ($productLevelTwo) {
                return $productLevelTwo->contains('id', $levelOne['id']);
            })->values();
            return $tags;
        } else {
            $tags = $productLevelFour->reject(function ($levelOne) use ($productLevelThree) {
                return $productLevelThree->contains('id', $levelOne['id']);
            })->values();
            return $tags;
        }
    }
}
