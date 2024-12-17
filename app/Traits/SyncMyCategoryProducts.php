<?php

namespace App\Traits;

use App\Models\Product;
use App\Models\Attribute;
use Illuminate\Support\Facades\Log;
use APP\Models\StandardTag;
use Modules\Automotive\Entities\DreamCar;
use App\Jobs\SyncMyCategoryProduct;

trait SyncMyCategoryProducts
{
    /**
     * Sync the product with categories based on the products and categories attributes.
     */

     public static function syncProductToCategory($module, $product, $partailUpdate = false) {
        $module = StandardTag::where('slug', $module)->orWhere('id', $module)->firstOrFail();
        $product->dreamCars()->detach();     
        if($partailUpdate) {
            $levelTwo = $product->standardTags()->whereHas('levelTwo', function($query) use ($module) {
                $query->where('L1', $module?->id);
            })->first();
            $levelThree = $product->standardTags()->whereHas('levelThree', function($query) use ($module, $levelTwo) {
                $query->where('L1', $module?->id)->where('L2', $levelTwo?->id);
            })->first();
            $levelFour = $product->standardTags()->whereHas('tagHierarchies', function($query) use ($module, $levelTwo, $levelThree) {
                $query->where('L1', $module?->id)->where('L2', $levelTwo?->id)->where('L3', $levelThree?->id);
            })->first();

            $bedAttribute = Attribute::whereIn('slug', ['bed', 'beds'])->firstOrFail();
            $bed = $product->standardTags()->wherePivot('attribute_id', $bedAttribute?->id)->pluck('id')->toArray();

            $bathAttribute = Attribute::whereIn('slug', ['bath', 'baths'])->firstOrFail();
            $bath = $product->standardTags()->wherePivot('attribute_id', $bathAttribute?->id)->pluck('id')->toArray();
            if(count($bed) > 0 && count($bath) > 0) {
                $category = DreamCar::where([
                    ['module_id', $module?->id],
                    ['make_id', $levelTwo?->id],
                    ['model_id', $levelThree?->id],
                    ['level_four_tag_id', $levelFour?->id]
                ])->where('min_price', '<=', $product?->price)->where('max_price', '>=', $product?->price)
                    ->where(function($query) use ($bed, $bath) {
                        $query->whereIn('bed', $bed)->whereIn('bath', $bath);
                    })
                ->first()?->id;
                if($category) {
                    $product->dreamCars()->attach($category);
                }
            }
            
        } else {
            if($module?->slug == 'automotive')
            {
                $requestHierarchies = is_array(request()->hierarchies) ? request()->hierarchies : json_decode(request()->hierarchies, true);
                foreach ($requestHierarchies as $hierarchy) {
                    $category = DreamCar::where([
                        ['module_id', $module?->id],
                        ['make_id', $hierarchy['level_two_tag']],
                        ['model_id', $hierarchy['level_three_tag']],
                        ['level_four_tag_id', $hierarchy['level_four_tags']]
                    ])->whereRaw('CAST(`from` AS UNSIGNED) <= ?', [request()->input('year')])
                        ->whereRaw('CAST(`to` AS UNSIGNED) >= ?', [request()->input('year')])
                    ->first()?->id;
                    if($category) {
                        $product->dreamCars()->attach($category);
                    }
                }   
            } else if ($module->slug == 'real-estate') {
                $bedAttribute = Attribute::whereIn('slug', ['bed', 'beds'])->firstOrFail();
                $bed = $product->standardTags()->wherePivot('attribute_id', $bedAttribute?->id)->pluck('id')->toArray();
                $bathAttribute = Attribute::whereIn('slug', ['bath', 'baths'])->firstOrFail();
                $bath = $product->standardTags()->wherePivot('attribute_id', $bathAttribute?->id)->pluck('id')->toArray();
                if(count($bed) > 0 && count($bath) > 0) {
                    $price = floatval(str_replace(',', '', request()->input('price')));
                    $category = DreamCar::where([
                        ['module_id', $module?->id],
                        ['make_id', request()->input('level_two_tag')],
                        ['model_id', request()->input('level_three_tag')],
                        ['level_four_tag_id', request()->input('level_four_tags')]
                    ])->where('min_price', '<=', $price)->where('max_price', '>=', $price)
                        ->where(function($query) use ($bed, $bath) {
                            $query->whereIn('bed', $bed)->whereIn('bath', $bath);
                        })
                    ->first()?->id;
                    if($category) {
                        $product->dreamCars()->attach($category);
                    }
                }

            } else {
                $category = DreamCar::where([
                    ['module_id', $module?->id],
                    ['make_id', request()->input('level_two_tag')],
                    ['model_id', request()->input('level_three_tag')],
                    ['level_four_tag_id', request()->input('level_four_tags')]
                ])->when($module?->slug == 'boats', function($query) {
                    $query->whereRaw('CAST(`from` AS UNSIGNED) <= ?', [request()->input('year')])
                        ->whereRaw('CAST(`to` AS UNSIGNED) >= ?', [request()->input('year')]);
                })
                ->first()?->id;
                if($category) {
                    $product->dreamCars()->attach($category);
                }
            }
        }
     }
}
