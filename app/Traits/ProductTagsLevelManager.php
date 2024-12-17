<?php

namespace App\Traits;

use App\Models\Tag;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\ProductPriority;
use App\Traits\ManageMyCategory;
use DonatelloZa\RakePlus\RakePlus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\ModuleSessionManager;

trait ProductTagsLevelManager
{
    public static function checkProductTagsLevel($product)
{
    $currentModule = ModuleSessionManager::getModule();
    $modules = ['events', 'news', 'posts', 'blogs', 'obituaries', 'recipes', 'marketplace', 'taskers'];
    $moduleTag = $product->standardTags()->whereHas('levelOne')->first();
    if (!$moduleTag) {
        $moduleTag = $product->standardTags()->where('type', 'module')->first();
    }

    if (in_array($moduleTag->slug, $modules) || request()->input('frontendFlag')) {
        $levelOnes = $product->standardTags()->whereHas('levelOne')->pluck('id');
        $levelTwos = $product->standardTags()->whereHas('levelTwo')->pluck('id');
        $levelThree = $product->standardTags()->whereHas('levelThree')->pluck('id');
        $levelFour = $product->standardTags()->whereHas('tagHierarchies', function ($query) use ($levelOnes, $levelTwos, $levelThree) {
            $query->where('level_type', 4)->whereIn('L1', $levelOnes)->whereIn('L2', $levelTwos)->whereIn('L3', $levelThree);
        })->get();

        if (count($levelFour) <= 0 || count($levelThree) <= 0) {
            $product->previous_status = $product->status;
            $product->status = 'tags_error';
            $flag = false;
        } else {
            $status = $product->status;
            $product->status = 'active';
            $product->previous_status = $status;
            $flag = true;
        }

        // Skip main image check for "posts" module
        if (!$product->mainImage()->exists() && $currentModule != 'government' && $currentModule != 'posts') {
            $product->status = 'inactive';
            $product->previous_status = 'active';
            $flag = false;
        }

        if ($product->status == 'active') {
            ManageMyCategory::manageProductCategory($product, $moduleTag, $levelOnes, $levelThree, $levelFour);
        }

        $product->saveQuietly();
        return $flag;
    } else {
        $flag = false;
        $levelOnes = $product->business->standardTags()->whereHas('levelOne')->pluck('id');
        $levelTwos = $product->business->standardTags()->whereHas('levelTwo', function ($query) use ($levelOnes) {
            $query->where('L1', $levelOnes);
        })->first() ? $product->business->standardTags()->whereHas('levelTwo', function ($query) use ($levelOnes) {
            $query->where('L1', $levelOnes);
        })->pluck('id') : $product->standardTags()->whereHas('levelTwo', function ($query) use ($levelOnes) {
            $query->where('L1', $levelOnes);
        })->pluck('id');

        $levelThree = $product->standardTags()->whereHas('levelThree')->pluck('id');

        $levelFour = $product->standardTags()->whereHas('tagHierarchies', function ($query) use ($levelOnes, $levelTwos, $levelThree) {
            $query->where('level_type', 4)->whereIn('L1', $levelOnes)->whereIn('L2', $levelTwos)->whereIn('L3', $levelThree);
        })->get();

        if (count($levelFour) <= 0 || count($levelThree) <= 0) {
            $product->previous_status = $product->status;
            $product->status = 'tags_error';
            $flag = false;
        } else {
            $status = $product->status;
            $product->status = 'active';
            $product->previous_status = $status;
            $flag = true;
        }

        $product->saveQuietly();
        return $flag;
    }
}


    public static function priorityOneTags($product, $removeTags = null, $case = null)
    {
        $p4Tags = [];
        $product = $product->active()->where('id', $product->id)->first();
        if($product){
            $hierarchyTags = $product->standardTags()->where(function ($query) {
                $query->whereHas('levelOne')
                    ->orwhereHas('levelTwo')
                    ->orwhereHas('levelThree')
                    ->orwhereHas('tagHierarchies');
            })->get();

            $orphanTags = [];
            foreach ($hierarchyTags as $tag) {
                $orphans = $tag->tags_()->whereHas('products', function ($query) use ($product) {
                    $query->where('id', $product->id);
                })->pluck('name')->toArray();
                $orphanTags =  Arr::collapse([$orphanTags, $orphans]);
            }

            $modules = ['events', 'news', 'posts', 'blogs', 'obituaries', 'recipes', 'marketplace', 'taskers'];
            $moduleTag = $product->standardTags()->whereHas('levelOne')->first();
            if (!$moduleTag) {
                $moduleTag = $product->standardTags()->where('type', 'module')->first();
            }
            if (in_array($moduleTag->slug, $modules)){
                // Retrieve level one, level two, and level three tags in a single query
                $levelTags = $product->standardTags()->with('levelOne', 'levelTwo', 'levelThree')->get();

                // Separate level tags into respective arrays
                $levelOnes = [];
                $levelTwos = [];
                $levelThrees = [];

                foreach ($levelTags as $tag) {
                    if ($tag->levelOne) {
                        $levelOnes[] = $tag->toArray();
                    }
                    if ($tag->levelTwo) {
                        $levelTwos[] = $tag->toArray();
                    }
                    if ($tag->levelThree) {
                        $levelThrees[] = $tag->toArray();
                    }
                }

                // Extract ids for level tags
                $levelOnesIds = array_column($levelOnes, 'id');
                $levelTwosIds = array_column($levelTwos, 'id');
                $levelThreesIds = array_column($levelThrees, 'id');

                // Retrieve level four tags using a single query with conditions
                $levelFour = $product->standardTags()->whereHas('tagHierarchies', function ($query) use ($levelOnesIds, $levelTwosIds, $levelThreesIds) {
                    $query->where('level_type', 4)
                        ->whereIn('L1', $levelOnesIds)
                        ->whereIn('L2', $levelTwosIds)
                        ->whereIn('L3', $levelThreesIds);
                })->get()->toArray();

                // marge the tags L1 to L4
                $mergedArray = array_merge($levelOnes, $levelTwos, $levelThrees, $levelFour);

                // list the name field only to save in P1
                $P1Tags = array_map(function ($item) {
                    return $item['name'];
                }, $mergedArray);
            }
            else{
                $P1Tags = Arr::collapse([$hierarchyTags->pluck('name')->toArray()]);
            }

            $removingTags = [];
            if ($removeTags) {
                if ($case == 'update' || $case == 'hierarchy' || $case == 'assign_tags') {
                    foreach ($removeTags as $id) {
                        $standardTag = StandardTag::find($id);
                        array_push($removingTags, $standardTag->name);
                        $tags = $standardTag->tags_()->whereHas('products', function ($query) use ($product) {
                            $query->where('id', $product->id);
                        })->get();
                        if ($tags->count() > 0) {
                            foreach ($tags as $tag) {
                                $standardTag = $tag->standardTags_()->where(function ($query) {
                                    $query->whereHas('levelThree')->orwhereHas('tagHierarchies');
                                    $query->orWhere('type', 'attribute')->orWhere('type', 'brand');
                                })->whereHas('productTags', function ($query) use ($product) {
                                    $query->where('id', $product->id);
                                })->first();
                                if (!$standardTag) {
                                    array_push($p4Tags, $tag->name);
                                }
                                array_push($removingTags, $tag->name);
                            }
                        }
                    }
                } else if ($case == 'mapping' || $case == 'product_tag') {
                    foreach ($removeTags as $id) {
                        $tag = Tag::find($id);
                        array_push($removingTags, $tag->name);
                        $standardTag = $tag->standardTags_()->where(function ($query) {
                            $query->whereHas('levelThree')->orwhereHas('tagHierarchies');
                            $query->orWhere('type', 'attribute')->orWhere('type', 'brand');
                        })->whereHas('productTags', function ($query) use ($product) {
                            $query->where('id', $product->id);
                        })->first();
                        if (!$standardTag) {
                            array_push($p4Tags, $tag->name);
                        }
                    }
                }
            }
            $existPriority = $product->priority()->first();
            if ($existPriority) {
                $p4 = $existPriority->P4;
                $p4Tags =  $p4 ? Arr::collapse([$p4Tags, $p4]) : $p4Tags;
                if (count($removingTags) > 0) {
                    if ($case == 'hierarchy') {
                        $P1Tags = array_diff($P1Tags, $removingTags);
                    }
                }
            }
            $P1Tags = array_values(array_unique(array_map('strtolower', $P1Tags), SORT_REGULAR));
            $p4Tags = array_diff($p4Tags, $P1Tags);
            $p4Tags = array_diff($p4Tags, $orphanTags);
            $p4Tags = array_values(array_unique(array_map('strtolower', $p4Tags), SORT_REGULAR));
            $product->priority()->updateOrCreate(
                ['product_id' => $product->id],
                [
                    'P1' => collect($P1Tags),
                    'P4' => collect($p4Tags)
                ]
            );
        }

    }

    public static function priorityFour($product, $tags = null, $flag = false, $remove = false)
    {
//        try {
//            $p1Tags = [];
//            $p2Tags = [];
//            $p3Tags = [];
//            $p4Tags = [];
//            if ($tags && count($tags) > 0) {
//                $orphanTags = Tag::whereIn('id', $tags)->whereHas('products', function ($query) use ($product) {
//                    $query->where('id', $product->id);
//                })->get();
//                foreach ($orphanTags as $key => $tag) {
//                    array_push($p4Tags, $tag->name);
//                    if ($tag->standardTags_->count() > 0) {
//                        $standardTags = $tag->standardTags_()->whereHas('productTags', function ($query) use ($product) {
//                            $query->where('id', $product->id);
//                        })->get();
//                        foreach ($standardTags as $key => $sTag) {
//                            if ($sTag->type == 'brand') {
//                                array_push($p3Tags, $sTag->name);
//                            } else if ($sTag->type == 'attribute') {
//                                array_push($p2Tags, $sTag->name);
//                            } else {
//                                $existInHierarchy = $sTag->where('id', $sTag->id)->where(function ($query) {
//                                    $query->whereHas('levelOne')
//                                        ->orWhereHas('levelTwo')
//                                        ->orWhereHas('levelThree')
//                                        ->orwhereHas('tagHierarchies');
//                                })->first();
//                                if ($existInHierarchy) {
//                                    array_push($p1Tags, $existInHierarchy->name);
//                                } else {
//                                    array_push($p4Tags, $tag->name);
//                                    array_push($p2Tags, $sTag->name);
//                                }
//                            }
//                        }
//                    }
//                }
//                $existPriority = $product->priority()->first();
//                if ($existPriority) {
//                    $p1 = $existPriority->P1;
//                    $p1Tags = Arr::collapse([$p1, $p1Tags]);
//                    $p2 = $existPriority->P2;
//                    $p2Tags = Arr::collapse([$p2, $p2Tags]);
//                    $p3 = $existPriority->P3;
//                    $p3Tags = Arr::collapse([$p3, $p3Tags]);
//                    if (!$remove) {
//                        $p4 = $existPriority->P4;
//                        $p4Tags = Arr::collapse([$p4, $p4Tags]);
//                    } else {
//                        $p4 = $existPriority->P4;
//                        $p4Tags = array_intersect($p4, $p4Tags);
//                        $p4Tags = array_diff($p4, $p4Tags);
//                    }
//                }
//                $diff = array_intersect($p4Tags, $p1Tags);
//                $p4Tags = array_diff($p4Tags, $diff);
//                $diff = array_intersect($p4Tags, $p2Tags);
//                $p4Tags = array_diff($p4Tags, $diff);
//                $diff = array_intersect($p4Tags, $p3Tags);
//                $p4Tags = array_diff($p4Tags, $diff);
//                //formating arrays
//                $p1Tags = array_values(array_unique(array_map('strtolower', $p1Tags), SORT_REGULAR));
//                $p2Tags = array_values(array_unique(array_map('strtolower', $p2Tags), SORT_REGULAR));
//                $p3Tags = array_values(array_unique(array_map('strtolower', $p3Tags), SORT_REGULAR));
//                $p4Tags = array_values(array_unique(array_map('strtolower', $p4Tags), SORT_REGULAR));
//                $product->priority()->updateOrCreate(
//                    ['product_id' => $product->id],
//                    ['P1' => collect($p1Tags), 'P2' => collect($p2Tags), 'P3' => collect($p3Tags), 'P4' => collect($p4Tags)]
//                );
//            }
//        } catch (\Throwable $th) {
//            Log::info($th->getMessage());
//        }
        $product = $product->active()->where('id', $product->id)->first();
        if($product){
            $p4Tags = $product->tags()->where(function ($query) {
                $query->where('priority', '4');
            })->pluck('name')->toArray();

            $p4Tags = array_values(array_unique(array_map('strtolower', $p4Tags), SORT_REGULAR));
            $product->priority()->updateOrCreate(
                ['product_id' => $product->id],
                [
                    'P4' => collect($p4Tags)
                ]
            );
        }
    }

    // change orphan tags priority
    public static function orphanTagsPriority($standardTag)
    {
        $orphanTags = $standardTag->tags_()->get();
        foreach ($orphanTags as $tag) {
            $tag->update([
                'type' => $standardTag->type,
                'priority' => $standardTag->priority
            ]);
        }
    }

    public static function priorityTwoTags($product, $tags = null, $removeTags = null, $case = null, $removeStandardTag = null)
    {
        // $P2Tags = [];
        // $P4Tags = [];
        // $attributeTags = $product->standardTags()->whereType('attribute')->get();
        // if ($tags) {
        //     $P2Tags = $tags;
        // }
        // $removingTags = [];
        // if ($removeTags) {
        //     foreach ($removeTags as $id) {
        //         if ($case) {
        //             if ($case == 'attribute') {
        //                 $standardTag = StandardTag::find($id);
        //                 array_push($removingTags, $standardTag->name);
        //                 $currentProduct = $standardTag->productTags()->where('id', $product->id)->first();
        //                 if ($currentProduct) {
        //                     array_push($P4Tags, $standardTag->name);
        //                 }
        //                 $tags = $standardTag->tags_()->whereHas('products', function ($query) use ($product) {
        //                     $query->where('id', $product->id);
        //                 })->get();
        //                 foreach ($tags as $tag) {
        //                     $standardTag = $tag->standardTags_()->where('id', '<>', $id)->where(function ($query) {
        //                         $query->whereHas('levelThree')->orwhereHas('tagHierarchies');
        //                         $query->orWhere('type', 'attribute')->orWhere('type', 'brand');
        //                     })->whereHas('productTags', function ($query) use ($product) {
        //                         $query->where('id', $product->id);
        //                     })->first();
        //                     if (!$standardTag) {
        //                         array_push($P4Tags, $tag->name);
        //                     }
        //                     array_push($removingTags, $tag->name);
        //                 }
        //             } else if ($case == 'mapping') {
        //                 $tag = Tag::where('id', $id)->whereHas('products', function ($query) use ($product) {
        //                     $query->where('id', $product->id);
        //                 })->first();
        //                 if ($tag) {
        //                     $standardTag = $tag->standardTags_()->where('id', '<>', $id)->where(function ($query) {
        //                         $query->whereHas('levelThree')->orwhereHas('tagHierarchies');
        //                         $query->orWhere('type', 'attribute')->orWhere('type', 'brand');
        //                     })->whereHas('productTags', function ($query) use ($product) {
        //                         $query->where('id', $product->id);
        //                     })->first();
        //                     if (!$standardTag) {
        //                         array_push($P4Tags, $tag->name);
        //                     }
        //                     array_push($removingTags, $tag->name);
        //                 }
        //                 if ($removeStandardTag) {
        //                     array_push($removingTags, $removeStandardTag->name);
        //                 }
        //             } else if ($case == 'assign_tags') {
        //                 $tag = Tag::find($id);
        //                 array_push($removingTags, $tag->name);
        //                 $tags = $tag->standardTags_()->pluck('name')->toArray();
        //                 $removingTags = Arr::collapse([$removingTags, $tags]);
        //             }
        //         }
        //     }
        // }
        // $orphanTags = [];
        // if (count($attributeTags) > 0) {
        //     $P2Tags = Arr::collapse([$P2Tags, $attributeTags->pluck('name')->toArray()]);
        //     foreach ($attributeTags as $tag) {
        //         $orphans = $tag->tags_()->whereHas('products', function ($query) use ($product) {
        //             $query->where('id', $product->id);
        //         })->pluck('name')->toArray();
        //         $orphanTags =  Arr::collapse([$orphanTags, $orphans]);
        //     }
        // }
        // $P2Tags =  Arr::collapse([$P2Tags]);
        // $existPriority = $product->priority()->first();
        // if ($existPriority) {
        //     $p2 = $existPriority->P2;
        //     $P2Tags = Arr::collapse([$P2Tags, $p2]);
        //     $p4 = $existPriority->P4;
        //     if ($p4) {
        //         $P4Tags = Arr::collapse([$P4Tags, $p4]);
        //     }
        //     if (count($removingTags) > 0) {
        //         $P2Tags = array_diff($P2Tags,  array_map('strtolower', $removingTags));
        //     }
        // }
        // $P2Tags = array_values(array_unique(array_map('strtolower', $P2Tags), SORT_REGULAR));
        // $P4Tags = array_diff($P4Tags, $P2Tags);
        // $P4Tags = array_diff($P4Tags, $orphanTags);
        // $P4Tags = array_values(array_unique(array_map('strtolower', $P4Tags), SORT_REGULAR));
        // $product->priority()->updateOrCreate(
        //     ['product_id' => $product->id],
        //     [
        //         'P2' => collect($P2Tags),
        //         'P4' => collect($P4Tags)
        //     ]
        // );

        $product = $product->active()->where('id', $product->id)->first();
        if($product){
            $moduleTag = $product->standardTags()->whereHas('levelOne')->first();
            if ($moduleTag?->slug == 'notices' || $moduleTag?->slug == 'government')
            {
                $productNameArray = str_word_count(strip_tags($product->description), 1);
                $tags = RakePlus::create(strip_tags($product->description))->keywords();

                $P2Tags = array_map(function ($tag) use ($productNameArray) {
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

            }
            else{
                $productNameArray = str_word_count($product->name, 1);
                $tags = RakePlus::create($product->name)->keywords();

                $P2Tags = array_map(function ($tag) use ($productNameArray) {
                    $matchedProducts = [];

                    foreach ($productNameArray as $productName) {
                        if (strlen($productName) >= 3 && strpos(strtolower($tag), strtolower($productName)) !== false) {
                            $productParts = explode('/', $productName);

                            foreach ($productParts as $part) {
                                if (strpos(strtolower($tag), strtolower($part)) !== false) {
                                    $matchedProducts[] = $part;
                                }
                            }
                        }

                        $productNameCharacters = str_split(strtolower($productName));
                        if (count($productNameCharacters) >= 3 && preg_match('/[\'",\/#!$%\^&\*;:{}=\-_`~()]/', $productName)) {
                            foreach (str_word_count($tag, 1) as $tagWord) {
                                $tagWordCharacters = str_split(strtolower($tagWord));
                                $intersect = array_intersect($productNameCharacters, $tagWordCharacters);
                                if (count($intersect) >= 3) {
                                    $matchedProducts[] = $productName;
                                }
                            }
                        }
                    }

                    if (!empty($matchedProducts)) {
                        return implode(' ', array_unique($matchedProducts));
                    } else {
                        return $tag;
                    }
                }, $tags);
            }

            // Convert to lowercase and ensure unique values
            $P2Tags = array_values(array_unique(array_map('strtolower', $P2Tags), SORT_REGULAR));

            $product->priority()->updateOrCreate(
                ['product_id' => $product->id],
                ['P2' => collect($P2Tags)]
            );
        }
    }

    public static function priorityThree($product, $tags = null, $remove = false, $standardTags = null, $removeStandardTag = null, $removeTags = null)
    {
//        try {
//            $p4Tags = [];
//            $p4StandardTags = [];
//            $removeP4Tags = [];
//            if ($tags && count($tags) > 0) {
//                $p3Tags = [];
//                $orphanTags = Tag::whereIn('id', $tags)->whereHas('products', function ($query) use ($product) {
//                    $query->where('id', $product->id);
//                })->get();
//                foreach ($orphanTags as $key => $tag) {
//                    if ($remove) {
//                        $standardTag = $tag->standardTags_()->where(function ($query) {
//                            $query->whereHas('levelThree')->orwhereHas('tagHierarchies');
//                            $query->orWhere('type', 'attribute')->orWhere('type', 'brand');
//                        })->whereHas('productTags', function ($query) use ($product) {
//                            $query->where('id', $product->id);
//                        })->first();
//                        if (!$standardTag) {
//                            array_push($p4Tags, $tag->name);
//                        }
//                    }
//                    if ($tag->standardTags_->count() > 0) {
//                        $standardTag = $tag->standardTags_()->whereType('brand')->pluck('name')->toArray();
//                        $p3Tags = Arr::collapse([$p3Tags, $standardTag]);
//                        if ($remove) {
//                            $standardTag = $tag->standardTags_()->where('priority', 4)->pluck('name')->toArray();
//                            $p4StandardTags = Arr::collapse([$p4StandardTags, $standardTag]);
//                        } else {
//                            array_push($removeP4Tags, $tag->name);
//                        }
//                    }
//                }
//                $existPriority = $product->priority()->first();
//                if ($existPriority) {
//                    if (!$remove) {
//                        $p3 = $existPriority->P3;
//                        $p3Tags = Arr::collapse([$p3, $p3Tags]);
//                    } else {
//                        $p3 = $existPriority->P3;
//                        $p3Tags = array_intersect($p3, $p3Tags);
//                        $p3Tags = array_diff($p3, $p3Tags);
//                    }
//                }
//            }
//            if ($standardTags && count($standardTags) > 0) {
//                $p3Tags = [];
//                $standardTags = StandardTag::whereIn('id', $standardTags)->get();
//                foreach ($standardTags as $key => $tag) {
//                    array_push($p3Tags, $tag->name);
//                    if ($tag->tags_()->count() > 0) {
//                        $orphanTags = $tag->tags_()->whereHas('products', function ($query) use ($product) {
//                            $query->where('id', $product->id);
//                        })->get();
//                        foreach ($orphanTags as $key => $orphanTag) {
//                            if ($remove) {
//                                $standardTag = $orphanTag->standardTags_()->where(function ($query) {
//                                    $query->whereHas('levelThree')->orwhereHas('tagHierarchies');
//                                    $query->orWhere('type', 'attribute')->orWhere('type', 'brand');
//                                })->whereHas('productTags', function ($query) use ($product) {
//                                    $query->where('id', $product->id);
//                                })->first();
//                                if (!$standardTag) {
//                                    array_push($p4Tags, $orphanTag->name);
//                                }
//                            }
//                        }
//                    }
//                }
//                $existPriority = $product->priority()->first();
//                if ($existPriority) {
//                    if (!$remove) {
//                        $p3 = $existPriority->P3;
//                        $p3Tags = Arr::collapse([$p3, $p3Tags]);
//                    } else {
//                        $p3 = $existPriority->P3;
//                        $p3Tags = array_map('strtolower', $p3Tags);
//                        $p3Tags = array_intersect($p3, $p3Tags);
//                        $p3Tags = array_diff($p3, $p3Tags);
//                    }
//                }
//            }
//            if (($tags && count($tags) > 0) || ($standardTags && count($standardTags) > 0)) {
//                $p3Tags = array_diff(array_map('strtolower', $p3Tags), array_map('strtolower', $p4StandardTags));
//                $p3Tags = array_values(array_unique(array_map('strtolower', $p3Tags), SORT_REGULAR));
//                $existPriority = $product->priority()->first();
//                if ($existPriority) {
//                    if ($removeStandardTag) {
//                        $tag = $removeStandardTag->tags_()->whereHas('products', function ($query) use ($product) {
//                            $query->where('id', $product->id);
//                        })->first();
//                        if (!$tag) {
//                            $p3Tags = array_diff($p3Tags, [Str::lower($removeStandardTag->name)]);
//                        }
//                    }
//                    $p4 = $existPriority->P4;
//                    if ($p4) {
//                        $p4Tags = Arr::collapse([$p4Tags, $p4]);
//                    }
//                }
//                $p3Tags = array_values(array_unique(array_map('strtolower', $p3Tags), SORT_REGULAR));
//                $p4Tags = array_diff($p4Tags, $p3Tags);
//                $p4Tags = Arr::collapse([$p4Tags, $p4StandardTags]);
//                $p4Tags = array_diff($p4Tags, $removeP4Tags);
//                $p4Tags = array_values(array_unique(array_map('strtolower', $p4Tags), SORT_REGULAR));
//                $product->priority()->updateOrCreate(
//                    ['product_id' => $product->id],
//                    [
//                        'P3' => collect($p3Tags),
//                        'P4' => collect($p4Tags)
//                    ]
//                );
//            }
//
//            if ($removeTags) {
//                $removeingTags = [];
//                $p4Tags = [];
//                foreach ($removeTags as $id) {
//                    $brandTag = StandardTag::find($id);
//                    array_push($removeingTags, $brandTag->name);
//                    if ($brandTag->tags_()->count() > 0) {
//                        $orphanTags = $brandTag->tags_()->whereHas('products', function ($query) use ($product) {
//                            $query->where('id', $product->id);
//                        })->get();
//                        foreach ($orphanTags as $key => $orphanTag) {
//                            $standardTag = $orphanTag->standardTags_()->where(function ($query) {
//                                $query->whereHas('levelThree')->orwhereHas('tagHierarchies');
//                                $query->orWhere('type', 'attribute')->orWhere('type', 'brand');
//                            })->whereHas('productTags', function ($query) use ($product) {
//                                $query->where('id', $product->id);
//                            })->first();
//                            if (!$standardTag) {
//                                array_push($p4Tags, $orphanTag->name);
//                            }
//                            array_push($removeingTags, $orphanTag->name);
//                        }
//                    }
//
//                    $existPriority = $product->priority()->first();
//                    if ($existPriority) {
//                        $p3 = $existPriority->P3;
//                        $p3Tags = array_diff(array_map('strtolower', $p3), array_map('strtolower', $removeingTags));
//                        $p4 = $existPriority->P4;
//                        if ($p4) {
//                            $p4Tags = Arr::collapse([$p4Tags, $p4]);
//                        }
//                    }
//                    $p4Tags = array_values(array_unique(array_map('strtolower', $p4Tags), SORT_REGULAR));
//                    $p3Tags = array_values(array_unique(array_map('strtolower', $p3Tags), SORT_REGULAR));
//                    $product->priority()->updateOrCreate(
//                        ['product_id' => $product->id],
//                        [
//                            'P3' => collect($p3Tags),
//                            'P4' => collect($p4Tags)
//                        ]
//                    );
//                }
//            }
//        } catch (\Throwable $th) {
//            Log::info($th->getMessage());
//        }
        $product = $product->active()->where('id', $product->id)->first();
        if($product){
            $p3Tags = $product->standardTags()->where(function ($query) {
                $query->where('type', 'attribute')->orWhere('type', 'brand');
            })->pluck('name')->toArray();

            $p3Tags = array_values(array_unique(array_map('strtolower', $p3Tags), SORT_REGULAR));
            $product->priority()->updateOrCreate(
                ['product_id' => $product->id],
                [
                    'P3' => collect($p3Tags)
                ]
            );
        }
    }
}
