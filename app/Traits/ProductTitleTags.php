<?php

namespace App\Traits;

use App\Models\Tag;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use App\Models\TagHierarchy;
use DonatelloZa\RakePlus\RakePlus;
use Illuminate\Support\Facades\Log;
use App\Traits\ProductTagsLevelManager;
use DonatelloZa\RakePlus\StopwordArray;

trait ProductTitleTags
{
    /**
     * To create tags from product title
     *
     * @param $product
     */
    public static function createTag($product, $remove = null)
    {
        $oldProduct = Product::find($product->id);
        //split title into tags and create or update orphan tags
        self::splitTitleIntoTags($product, $oldProduct, $remove);
    }


    private static function splitTitleIntoTags($product, $oldProduct, $remove = null)
    {
        $priorityArray = [];
        $allTags = [];
        $existInExtra = null;
        $productNameArray = str_word_count($product->name, 1);
        $tags = RakePlus::create($product->name)->keywords();
        $tags = array_map(function ($tag) use ($productNameArray) {
            foreach ($productNameArray as $productName) {
                if (strlen($productName) >= 3 && strpos(strtolower($tag), strtolower($productName)) !== false) {
                    return $productName;
                }
                $productNameCharacters = str_split(strtolower($productName));
                if (count($productNameCharacters) >= 3 && preg_match('/[\'",\/#!$%\^&\*;:{}=\-_`~()]/', $productName)) {
                    foreach (str_word_count($tag, 1) as $tagWord) {
                        $tagWordCharacters = str_split(strtolower($tagWord));
                        $intersect = array_intersect($productNameCharacters, $tagWordCharacters);
                        if (count($intersect) >= 3) {
                            return $productName;
                        }
                    }
                }
            }
            return $tag;
        }, $tags);

        //
        $oldProductNameArray = str_word_count($oldProduct->name, 1);
        $oldTags = RakePlus::create($oldProduct->name)->keywords();
        $oldTags = array_map(function ($tag) use ($oldProductNameArray) {
            foreach ($oldProductNameArray as $productName) {
                if (strlen($productName) >= 3 && strpos(strtolower($tag), strtolower($productName)) !== false) {
                    return $productName;
                }
                $productNameCharacters = str_split(strtolower($productName));
                if (count($productNameCharacters) >= 3) {
                    foreach (str_word_count($tag, 1) as $tagWord) {
                        $tagWordCharacters = str_split(strtolower($tagWord));
                        $intersect = array_intersect($productNameCharacters, $tagWordCharacters);
                        if (count($intersect) >= 3) {
                            return $productName;
                        }
                    }
                }
            }
            return $tag;
        }, $oldTags);

        $removedTags = array_diff($oldTags, $tags);

        foreach ($tags as $tagname) {
            $tag = removeSpecialCharacters($tagname);
            if ($tag && !empty(orphanTagSlug($tag))) {
                $tag = Tag::updateOrCreate([
                    'slug' => orphanTagSlug($tag)
                ], [
                    'name' => $tag,
                    'priority' => 2,
                    'is_category' => 0,
                    'is_show' => \true
                ]);
                $business = $product->business()->first();
                if ($business) {
                    $existInExtra = $tag->businesses()->where('id', $business->id)->first();
                }

                if (!$existInExtra) {
                    $tag->update([
                        'is_show' => true
                    ]);
                }
                \array_push($priorityArray, $tag->name);
            }
            // product tag relation
            if (isset($tag->id)) {
                if (!$product->ignoredTags()->whereJsonContains('tags', $tag->name)->exists()) {
                    $productTag = $product->tags()->where('tag_id', $tag->id)->first();
                    if (!$productTag) {
                        $product->tags()->attach($tag->id);
                    }
                    // check standard tag
                    self::standardTag($product, $tag, false, $remove);
                    \array_push($allTags, $tag->id);
                }
            }
        }

        if (count($removedTags) > 0) {
            self::removeTag($removedTags, $product);
        }

        if (\count($allTags) > 0) {
            ProductTagsLevelManager::priorityFour($product, $allTags);
        }
    }

    // check standard tag exist or not
    private static function standardTag($product, $tag, $remove = false, $removeTag = null)
    {
        $business = $product->business;
        $mappedStandardTags = $tag->standardTags_()->get();
        if ($mappedStandardTags->count() > 0) {
            foreach ($mappedStandardTags as $standardTag) {
                $productStandardTag = $standardTag->productTags()->where('product_id', $product->id)->first();
                if (!$productStandardTag) {
                    $existInBothLevels = $standardTag->whereHas('levelThree')->whereHas('tagHierarchies')->where('id', $standardTag->id)->first();
                    if ($existInBothLevels) {
                        $productLevelThreeTag = $product->standardTags()->where('id', '<>', $standardTag->id)->whereHas('levelTHree')->first();
                        if ($productLevelThreeTag) {
                            $exsistInlevelFour = $standardTag->whereHas('tagHierarchies', function ($query) use ($productLevelThreeTag) {
                                $query->where('L3', $productLevelThreeTag->id);
                            })->where('id', $standardTag->id)->first();
                            if ($exsistInlevelFour) {
                                $product->standardTags()->syncWithOutDetaching($standardTag->id);
                            }
                        } else {
                            $checkBusinessLevelThreeTags = $business->standardTags()->where('id', $standardTag->id)->whereHas('levelThree')->first();
                            if ($checkBusinessLevelThreeTags) {
                                $product->standardTags()->syncWithOutDetaching($standardTag->id);
                            }
                        }
                    } else {
                        $levelThreeTag = TagHierarchy::where('L3', $standardTag->id)->first();
                        if ($levelThreeTag) {
                            $productLevelThreeTag = $product->standardTags()->whereHas('levelThree')->first();
                            if (!$productLevelThreeTag) {
                                $levelThreeTags = $business->standardTags()->whereHas('levelThree')->pluck('id')->toArray();
                                if (in_array($standardTag->id, $levelThreeTags)) {
                                    $product->standardTags()->syncWithoutDetaching($standardTag->id);
                                }
                            }
                        } else {
                            $standardTag->productTags()->syncWithoutDetaching($product->id);
                        }
                    }
                }
                if ($remove) {
                    $standardTag->productTags()->detach($product->id);
                    $removeTags = $standardTag->where('id', $standardTag->id)->pluck('id')->toArray();
                    if ($standardTag->type == 'attribute') {
                        ProductTagsLevelManager::priorityTwoTags($product, null, $removeTags, 'attribute');
                    } else if ($standardTag->type == 'brand') {
                        ProductTagsLevelManager::priorityThree($product, null, true, $removeTags);
                    }
                }
            }
        } else {
            $standardTag = StandardTag::where('name', $tag->name)->where('type', '!=', 'module')->first();
            if ($standardTag) {
                $productStandardTag = $standardTag->productTags()->where('product_id', $product->id)->first();
                if (!$productStandardTag) {
                    $existInBothLevels = $standardTag->whereHas('levelThree')->whereHas('tagHierarchies')->where('id', $standardTag->id)->first();
                    if ($existInBothLevels) {
                        $productLevelThreeTag = $product->standardTags()->where('id', '<>', $standardTag->id)->whereHas('levelTHree')->first();
                        if ($productLevelThreeTag) {
                            $exsistInlevelFour = $standardTag->whereHas('tagHierarchies', function ($query) use ($productLevelThreeTag) {
                                $query->where('L3', $productLevelThreeTag->id);
                            })->where('id', $standardTag->id)->first();
                            if ($exsistInlevelFour) {
                                $product->standardTags()->syncWithOutDetaching($standardTag->id);
                            }
                        } else {
                            $checkBusinessLevelThreeTags = $business->standardTags()->where('id', $standardTag->id)->whereHas('levelThree')->first();
                            if ($checkBusinessLevelThreeTags) {
                                $product->standardTags()->syncWithOutDetaching($standardTag->id);
                            }
                        }
                    } else {
                        $levelThreeTag = TagHierarchy::where('L3', $standardTag->id)->first();
                        if ($levelThreeTag) {
                            $productLevelThreeTag = $product->standardTags()->whereHas('levelThree')->first();
                            if (!$productLevelThreeTag) {
                                $levelThreeTags = $business->standardTags()->whereHas('levelThree')->pluck('id')->toArray();
                                if (in_array($standardTag->id, $levelThreeTags)) {
                                    $product->standardTags()->syncWithoutDetaching($standardTag->id);
                                }
                            }
                        } else {
                            $standardTag->productTags()->syncWithoutDetaching($product->id);
                        }
                    }
                }
                if ($removeTag) {
                    $product->standardTags()->detach($removeTag);
                }
                // remove product standard tag
                if ($remove) {
                    $standardTag->productTags()->detach($product->id);
                    $removeTags = $standardTag->where('id', $standardTag->id)->pluck('id')->toArray();
                    if ($standardTag->type == 'attribute') {
                        ProductTagsLevelManager::priorityTwoTags($product, null, $removeTags, 'attribute');
                    } else if ($standardTag->type == 'brand') {
                        ProductTagsLevelManager::priorityThree($product, null, true, $removeTags);
                    }
                } else {
                    $tag->update([
                        'type' => $standardTag->type,
                        'attribute_id' => $standardTag->attribute_id,
                    ]);
                    $tag->standardTags_()->syncWithOutDetaching($standardTag->id);
                    if ($standardTag->type == 'attribute') {
                        ProductTagsLevelManager::priorityTwoTags($product);
                    } else if ($standardTag->type == 'brand') {
                        $standardTags = $standardTag->where('id', $standardTag->id)->pluck('id')->toArray();
                        ProductTagsLevelManager::priorityThree($product, null, false, $standardTags);
                    }
                }
            }
        }
    }


    // remove tags
    public static function deleteTag(Product $product)
    {
        $productNameArray = str_word_count($product->name, 1);
        $tags = RakePlus::create($product->name)->keywords();
        $tags = array_map(function ($tag) use ($productNameArray) {
            foreach ($productNameArray as $productName) {
                if (strlen($productName) >= 3 && strpos(strtolower($tag), strtolower($productName)) !== false) {
                    return $productName;
                }
                $productNameCharacters = str_split(strtolower($productName));
                if (count($productNameCharacters) >= 3) {
                    foreach (str_word_count($tag, 1) as $tagWord) {
                        $tagWordCharacters = str_split(strtolower($tagWord));
                        $intersect = array_intersect($productNameCharacters, $tagWordCharacters);
                        if (count($intersect) >= 3) {
                            return $productName;
                        }
                    }
                }
            }
            return $tag;
        }, $tags);
        self::removeTag($tags, $product);
    }

    private static function removeTag($tags, $product)
    {
        foreach ($tags as $tagname) {
            $tagname = removeSpecialCharacters($tagname);
            $tag = Tag::where('name', $tagname)->first();
            if ($tag) {

                $product->tags()->detach($tag->id);
                $otherProduct = $tag->products()->first();
                if (!$tag->type && !$tag->attribute_id && !$tag->mapped_to && !$otherProduct) {
                    $tag->delete();
                }
                if ($tag->mapped_to) {
                    self::standardTag($product, $tag, true);
                }
            }
        }
    }

    public static function checkTagError($product)
    {
        if ($product->status == 'tags_error') {
            $parensIds = $product->standardTags()->whereHas('children')->pluck('id');
            $childrenIds = $product->standardTags()->whereHas('parent')->pluck('id');
            $totalTags = $product->standardTags()->whereHas('parent', function ($query) use ($parensIds) {
                $query->whereIn('parent_id', $parensIds);
            })->get();
            return count($totalTags) == count($childrenIds);
        }
        return true;
    }
}
