<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Tag;
use App\Models\Product;;

use App\Models\Business;;

use App\Models\Attribute;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use App\Models\TagHierarchy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Transformers\TagTransformer;
use App\Transformers\StandardTagTransformer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StandardTagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $module_id)
    {
        try {
            $business = Business::where('slug', $request->business_slug)->first();

            $levelTwoTag = $request->input('level_two_tag') ? $request->input('level_two_tag') : $request->input('levelTwoTagId');
            $limit = $request->input('limit') ? $request->input('limit') : \config()->get('settings.pagination_limit');
            $productTag = StandardTag::wherehas('productTags', function ($query) use ($business) {
                $query->where('status', 'active');
                $query->when(request()->input('business_slug'), function ($subQuery) use ($business) {
                    $subQuery->where('business_id', $business->id);
                });
            })->whereHas('levelThree', function ($subQuery) use ($module_id, $levelTwoTag) {
                $subQuery->where('L1', $module_id)->where(function ($query) use ($levelTwoTag) {
                    $query->where('L2', $levelTwoTag)->orWhere(function ($query) use ($levelTwoTag) {
                        $query->whereHas('levelTwo', function ($query) use ($levelTwoTag) {
                            $query->where('slug', $levelTwoTag);
                        });
                    });
                });
            })->withCount(['productTags' => function ($query) {
                $query->whereStatus('active');
            }])->orderBy('product_tags_count', 'desc')->active()->paginate($limit);
            $paginate = apiPagination($productTag, $limit);
            $productTags = (new StandardTagTransformer)->transformCollection($productTag);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $productTags,
                'meta' => $paginate
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($module_id, $levelTwoTagId, Request $request)
    {
        try {
            $limit = $request->input('limit') ? $request->input('limit') : \config()->get('settings.pagination_limit');
            $levelTwoTagId = $request->input('level_two_tag') ? $request->input('level_two_tag') : $levelTwoTagId;
            $levelThreeTag = $request->input('level_three_tag') ? $request->input('level_three_tag') : $request->input('levelThreeTagId');
            $productTag = StandardTag::whereHas('productTags', function ($query) use ($levelTwoTagId, $levelThreeTag, $module_id) {
                $query->whereHas('standardTags', function ($query) use ($levelTwoTagId, $levelThreeTag, $module_id) {
                    $query->whereIn('id', [$levelTwoTagId, $levelThreeTag])->orWhereIn('slug', [$levelTwoTagId, $levelThreeTag])
                        ->select('*', DB::raw('count(*) as total'))
                        ->having('total', '>=', 2);
                })->whereHas('business', function ($subQuery) {
                    $subQuery->active();
                });
                $query->active();
            })->where(function ($query) use ($module_id, $request, $levelTwoTagId, $levelThreeTag) {
                $query->whereHas('tagHierarchies', function ($subQuery) use ($module_id, $request, $levelTwoTagId, $levelThreeTag) {
                    $subQuery->where('L1', $module_id)
                        ->where(function ($query) use ($levelTwoTagId) {
                            $query->where('L2', $levelTwoTagId)
                                ->orWhere(function ($query) use ($levelTwoTagId) {
                                    $query->whereHas('levelTwo', function ($query) use ($levelTwoTagId) {
                                        $query->where('slug', $levelTwoTagId);
                                    });
                                });
                        })->where(function ($query) use ($levelThreeTag) {
                            $query->where('L3',  $levelThreeTag)->orWhere(function ($query) use ($levelThreeTag) {
                                $query->whereHas('levelThree', function ($query) use ($levelThreeTag) {
                                    $query->where('slug',  $levelThreeTag);
                                });
                            });
                        })->where('level_type', 4);
                });
            })->withCount(['productTags' => function ($query) {
                $query->whereStatus('active');
            }])->orderBy('product_tags_count', 'desc')->active()->paginate($limit);
            $paginate = apiPagination($productTag, $limit);
            $options = ['withProducts' => $request->boolean('withProducts'), 'withMinimumData' =>  $request->boolean('withMinimumData'), 'delivery_type' => $request->input('delivery_type')];
            $productTags = (new StandardTagTransformer)->transformCollection($productTag, $options);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $productTags,
                'meta' => $paginate
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // store specific tags
    public function getProductTag(Request $request, $module_id, $business)
    {
        try {
            $levelTwoTags = $request->level_two_tags;
            $levelThreeTag = $request->level_three_tag;
            $productTags = null;
            $options = [
                'withUpToLevelThreeProducts' => $request->input('withUpToLevelThreeProducts') ? ($request->input('withLevelFourTags') ? false : $request->input('withUpToLevelThreeProducts')) : false,
                'withLevelFourTags' => $request->input('withLevelFourTags') ? $request->input('withLevelFourTags') : false,
                'moduleId' => $module_id,
                'business' => $business,
                'deliveryType' => $request->has('delivery_type') ? $request->input('delivery_type') : null
            ];
            if (!$levelTwoTags) {
                $levelTwoTags = StandardTag::whereHas('productTags.business', function ($query) use ($business) {
                    $query->where('uuid', $business)->orWhere('slug', $business);
                })->where(function ($query) use ($module_id, $request, $levelTwoTags) {
                    $query->whereHas('LevelTwo', function ($subQuery) use ($module_id, $request, $levelTwoTags) {
                        $subQuery->where('L1', $module_id);
                    })->orWhereHas('tagHierarchies', function ($subQuery) use ($module_id, $request, $levelTwoTags) {
                        $subQuery->where('L1', $module_id)->where('level_type', 2);
                    });
                })->whereType('product')->active()->pluck('id')->toArray();
            }
            $options['level_two_tags']  = $levelTwoTags;
            if (!$levelThreeTag) {
                $productTags = StandardTag::whereHas('productTags.business', function ($query) use ($business) {
                    $query->where('uuid', $business)->orWhere('slug', $business);
                })->where(function ($query) use ($module_id, $request, $levelTwoTags) {
                    $query->whereHas('LevelThree', function ($subQuery) use ($module_id, $request, $levelTwoTags) {
                        $subQuery->where('L1', $module_id)->whereIn('L2', $levelTwoTags);
                    });
                })->whereType('product')->active()->get();
            } else {
                $productTags = StandardTag::whereHas('productTags', function ($query) use ($business, $levelTwoTags, $levelThreeTag) {
                    $query->whereHas('standardTags', function ($subQuery) use ($levelThreeTag, $levelTwoTags) {
                        $levelThree = StandardTag::where('slug', $levelThreeTag)->pluck('id')->toArray();
                        $ids = array_merge($levelThree, $levelTwoTags);
                        $subQuery->whereIn('id', $ids)->select('*',  DB::raw('count(*) as total'))->having('total', '>=', 2);
                    })->whereHas('business', function ($query) use ($business) {
                        $query->where('uuid', $business)->orWhere('slug', $business);
                    });
                })->where(function ($query) use ($module_id, $request, $levelTwoTags) {
                    $query->whereHas('tagHierarchies', function ($subQuery) use ($module_id, $request, $levelTwoTags) {
                        $subQuery->where('L1', $module_id)->where('level_type', 4)->where(function ($query) use ($request) {
                            $query->where('L3', $request->level_three_tag)->orWhere(function ($query) use ($request) {
                                $query->whereHas('levelThree', function ($query) use ($request) {
                                    $query->whereSlug($request->level_three_tag);
                                });
                            });
                        })->whereIn('L2', $levelTwoTags);
                    });
                })
                    ->whereType('product')->active()->get();
            }
            $productTags = (new StandardTagTransformer)->transformCollection($productTags, $options);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $productTags,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getFiltersWithTag(Request $request, $storeTagId, $tagId = null)
    {
        $modules = ['news', 'marketplace'];
        try {
            $filtersParams = request()->input('filters');
            if (!is_array($filtersParams)) {
                $filtersParams = (array)json_decode($filtersParams);
            }
            $filterOptions = [
                'withFilters' => true
            ];
            $moduleTag = StandardTag::where('id', $storeTagId)->orWhere('slug', $storeTagId)->first();
            $storeTagId = $moduleTag->id;
            $attributes = null;
            $tagId = request()->input('level_two_tag') ? request()->input('level_two_tag') : $tagId;
            $tags =  array_filter([$tagId, request()->input('level_three_tag'), request()->input('level_four_tag')]);
            $levelTwoTag = StandardTag::where('id', request()->input('level_two_tag'))->orWhere('slug', request()->input('level_two_tag'))->first();
            $levelThreeTag = StandardTag::where('id', request()->input('level_three_tag'))->orWhere('slug', request()->input('level_three_tag'))->first();
            $levelfourTag = StandardTag::where('id', request()->input('level_four_tag'))->orWhere('slug', request()->input('level_four_tag'))->first();
            // Gettings brand tags filter from stadard tag table
            // Fetch the active standard tags with related attributes and product tags
            $standardTagFilters = StandardTag::active()
                ->with([
                    // Fetch related attributes with specific conditions
                    'attribute' => function ($query) use ($storeTagId) {
                        $query->select('id', 'name')
                            ->whereRelation('moduleTags', 'id', $storeTagId);
                    },

                    // Fetch related product tags with specific conditions
                    'productTags' => function ($query) use ($tags, $filtersParams, $moduleTag, $modules) {
                        $query->select('products.id', 'products.name')
                            ->withPivot('standard_tag_id', 'product_id', 'attribute_id')
                            ->when($tags, function ($query, $tags) {
                                $query->whereIn('id', $tags);
                            })
                            ->when(count($tags) > 0, function ($query) use ($tags, $moduleTag) {
                                $query->whereHas('standardTags', function ($subQuery) use ($tags, $moduleTag) {
                                    $subQuery->where(function ($query) use ($tags) {
                                        $query->whereIn('id', $tags)
                                            ->orWhereIn('slug', $tags);
                                    })->select('*', DB::raw('count(*) as total'))
                                        ->having('total', count($tags));
                                });
                            })
                            ->when(isset($filtersParams) && count($filtersParams) > 0 && !(request()->input('business_uuid') || request()->input('business_slug')), function ($query) use ($filtersParams, $moduleTag) {
                                foreach ($filtersParams['filters'] as $value) {
                                    $attribute = Attribute::whereIn('slug', array_keys($value))->first();
                                    $query->whereHas('standardTags', function ($query) use ($value, $attribute, $moduleTag) {
                                        $query->whereIn('id', array_values($value))
                                            ->when(in_array($attribute?->slug, ['interior-color', 'exterior-color']) || in_array($moduleTag?->slug, ['real-estate']), function ($subQuery) use ($attribute) {
                                                $subQuery->where('product_standard_tag.attribute_id', $attribute->id);
                                            });
                                    });
                                }
                            })
                            ->when(isset($filtersParams) && count($filtersParams) > 0 && (request()->input('business_uuid') || request()->input('business_slug')), function ($query) use ($filtersParams, $moduleTag) {
                                if (array_key_exists("size", $filtersParams)) {
                                    $query->whereHas('standardTags', function ($subQuery) use ($filtersParams) {
                                        $subQuery->where(function ($query) use ($filtersParams) {
                                            $query->whereIn('id', $filtersParams['size']);
                                        })->select('*', DB::raw('count(*) as total'))
                                            ->having('total', count($filtersParams['size']));
                                    });
                                    unset($filtersParams['size']);
                                }
                                if (count($filtersParams) > 0) {
                                    if (in_array($moduleTag?->slug, ['real-estate', 'automotive', 'boats'])) {
                                        foreach ($filtersParams['filters'] as $value) {
                                            $attribute = Attribute::whereIn('slug', array_keys($value))->first();
                                            $query->whereHas('standardTags', function ($query) use ($value, $attribute, $moduleTag) {
                                                $query->whereIn('id', array_values($value))
                                                    ->when(in_array($attribute?->slug, ['interior-color', 'exterior-color']) || in_array($moduleTag?->slug, ['real-estate']), function ($subQuery) use ($attribute) {
                                                        $subQuery->where('product_standard_tag.attribute_id', $attribute->id);
                                                    });
                                            });
                                        }
                                    } else {
                                        $query->whereHas('tags', function ($subQuery) use ($filtersParams) {
                                            $subQuery->where(function ($query) use ($filtersParams) {
                                                $query->whereIn('id', Arr::flatten($filtersParams));
                                            })->select('*', DB::raw('count(*) as total'))
                                                ->having('total', count(Arr::flatten($filtersParams)));
                                        });
                                    }
                                }
                            })
                            // Ensure the tag belongs to an active business or user
                            ->when(!in_array($moduleTag->slug, $modules), function ($query) {
                                $query->where(function ($subQuery) {
                                    $subQuery->whereHas('business', function ($innerQuery) {
                                        $innerQuery->where('status', 'active');
                                    })->orWhereHas('user', function ($innerQuery) {
                                        $innerQuery->where('status', 'active');
                                    });
                                })->when(request()->input('business_uuid') || request()->input('business_slug'), function ($subQuery) {
                                    $subQuery->whereRelation('business', 'slug', request()->input('business_slug'));
                                })->when(request()->input('user_id'), function ($subQuery) {
                                    $subQuery->where('user_id', request()->input('user_id'));
                                });
                            })
                            ->where('status', 'active');
                    }
                ])->whereHas('productTags', function ($query) use ($tags, $levelTwoTag, $levelThreeTag, $levelfourTag, $filtersParams, $moduleTag, $modules) {
                    if (count($tags) > 0) {
                        $query->whereHas('standardTags', function ($query) use ($tags, $levelTwoTag, $levelThreeTag, $levelfourTag, $moduleTag) {
                            $query->where(function ($query) use ($levelTwoTag, $levelThreeTag, $levelfourTag, $moduleTag) {
                                $query->whereIn('id', [$moduleTag->id, $levelTwoTag->id, $levelThreeTag->id, $levelfourTag?->id]);
                            })->select('*', DB::raw('count(*) as total'))
                                ->when($levelfourTag, function ($query) use ($levelTwoTag, $levelThreeTag, $levelfourTag, $moduleTag) {
                                    $query->having('total', count([$moduleTag->id, $levelTwoTag->id, $levelThreeTag->id, $levelfourTag?->id]));
                                }, function ($query) use ($levelTwoTag, $levelThreeTag, $moduleTag) {
                                    $query->having('total', '>=', count([$moduleTag->id, $levelTwoTag->id, $levelThreeTag->id]));
                                });
                        });
                    }
                    if (isset($filtersParams) && count($filtersParams) > 0 && !(request()->input('business_uuid') || request()->input('business_slug'))) {
                        foreach ($filtersParams['filters'] as $value) {
                            $attribute = Attribute::whereIn('slug', array_keys($value))->first();
                            $query->whereHas('standardTags', function ($query) use ($value, $attribute, $moduleTag) {
                                $query->whereIn('id', array_values($value))
                                    ->when(in_array($attribute?->slug, ['interior-color', 'exterior-color']) || in_array($moduleTag?->slug, ['real-estate']), function ($subQuery) use ($attribute) {
                                        $subQuery->where('product_standard_tag.attribute_id', $attribute->id);
                                    });
                            });
                        }
                    }
                    if (isset($filtersParams) && count($filtersParams) > 0 && (request()->input('business_uuid') || request()->input('business_slug'))) {
                        if (array_key_exists("size", $filtersParams)) {
                            $query->whereHas('standardTags', function ($subQuery) use ($filtersParams) {
                                $subQuery->where(function ($query) use ($filtersParams) {
                                    $query->whereIn('id', $filtersParams['size']);
                                })->select('*', DB::raw('count(*) as total'))
                                    ->having('total', count($filtersParams['size']));
                            });
                            unset($filtersParams['size']);
                        }
                        if (count($filtersParams) > 0) {
                            if (in_array($moduleTag?->slug, ['real-estate', 'automotive', 'boats'])) {
                                foreach ($filtersParams['filters'] as $value) {
                                    $attribute = Attribute::whereIn('slug', array_keys($value))->first();
                                    $query->whereHas('standardTags', function ($query) use ($value, $attribute, $moduleTag) {
                                        $query->whereIn('id', array_values($value))
                                            ->when(in_array($attribute?->slug, ['interior-color', 'exterior-color']) || in_array($moduleTag?->slug, ['real-estate']), function ($subQuery) use ($attribute) {
                                                $subQuery->where('product_standard_tag.attribute_id', $attribute->id);
                                            });
                                    });
                                }
                            } else {
                                $query->whereHas('tags', function ($subQuery) use ($filtersParams) {
                                    $subQuery->where(function ($query) use ($filtersParams) {
                                        $query->whereIn('id', Arr::flatten($filtersParams));
                                    })->select('*', DB::raw('count(*) as total'))
                                        ->having('total', count(Arr::flatten($filtersParams)));
                                });
                            }
                        }
                    }
                    $query->where(function ($subQuery) {
                        $subQuery->whereHas('business', function ($innerQuery) {
                            $innerQuery->where('status', 'active');
                        })->orWhereHas('user', function ($innerQuery) {
                            $innerQuery->where('status', 'active');
                        });
                    })->when(request()->input('business_uuid') || request()->input('business_slug'), function ($subQuery) {
                        $subQuery->whereRelation('business', 'slug', request()->input('business_slug'));
                    })->when(request()->input('user_id'), function ($subQuery) {
                        $subQuery->where('user_id', request()->input('user_id'));
                    });
                })
                ->where('status', 'active')
                ->get();


            $attributes = $standardTagFilters->where('type', 'attribute')
                ->pluck('attribute.*')
                ->flatten()
                ->unique(function ($item) {
                    return $item['id'];
                });

            $attributes = $attributes->sortBy(function ($attribute) {
                return $this->customOrder($attribute['slug']);
            })->values()->pluck('id');
            $data = [];

            //Price Filter
            $price = Product::where(function ($query) {
                $query->whereHas('business', function ($subQuery) {
                    if (request()->input('business_uuid') || request()->input('business_slug')) {
                        $subQuery->where('uuid', request()->input('business_uuid'))
                            ->orWhere('slug', request()->input('business_slug'))
                            ->whereStatus('active');
                    }
                    $subQuery->where('status', 'active');
                })->orWhereHas('user', function ($subQuery) {
                    // need for a query to specific user is remaining
                    $subQuery->where('status', 'active');
                });
            })->whereHas('standardTags', function ($query) use ($tagId, $tags) {
                if (count($tags) > 0) {
                    $query->where(function ($query) use ($tags) {
                        $query->whereIn('id', $tags)->orWhereIn('slug', $tags);
                    })
                        ->select('*', DB::raw('count(*) as total'))
                        ->having('total', count($tags));
                }
            })->select([DB::raw('FLOOR(min(price)) as min_price'), DB::raw("CASE WHEN '$moduleTag?->slug' = 'events' THEN MAX(max_price) ELSE MAX(price) END as max_price")])->first();

            if ($price->min_price && $price->max_price && ($price->min_price != $price->max_price)) {
                $data['price'] = $price;
            }
            // looping through to all attribute to get filters by attrubute type
            foreach ($attributes as $id) {
                $attribute = Attribute::whereHas('moduleTags', function ($query) use ($storeTagId) {
                    $query->where('id', $storeTagId);
                })->find($id);
                if ((request()->input('business_uuid') || request()->input('business_slug')) && !in_array($moduleTag?->slug, ['real-estate', 'automotive', 'boats'])) {
                    if ($attribute->slug != 'size') {
                        $OrphanTagfilters = Tag::active()->whereHas('products', function ($query) use ($tags, $filtersParams) {
                            $query->whereHas('standardTags', function ($subQuery) use ($tags, $filtersParams) {
                                if (count($tags) > 0) {
                                    $subQuery->where(function ($query) use ($tags) {
                                        $query->whereIn('id', $tags)->orWhereIn('slug', $tags);
                                    })->select('*', DB::raw('count(*) as total'))
                                        ->having('total', count($tags));
                                }
                            });
                            if (array_key_exists("size", $filtersParams)) {
                                $query->whereHas('standardTags', function ($subQuery) use ($filtersParams) {
                                    $subQuery->where(function ($query) use ($filtersParams) {
                                        $query->whereIn('id', $filtersParams['size']);
                                    })->select('*', DB::raw('count(*) as total'))
                                        ->having('total', count($filtersParams['size']));
                                });
                                unset($filtersParams['size']);
                            }
                            if (count($filtersParams) > 0) {
                                $query->whereHas('tags', function ($subQuery) use ($filtersParams) {
                                    $subQuery->whereIn('id', Arr::flatten($filtersParams));
                                });
                            }
                            $query->whereHas('business', function ($subQuery) {
                                $subQuery->where('uuid', request()->input('business_uuid'))->orWhere('slug', request()->input('business_slug'));
                                $subQuery->where('status', 'active');
                            });
                        })->whereHas('standardTags_', function ($query) use ($id) {
                            $query->whereHas('attribute', function ($subQuery) use ($id) {
                                $subQuery->where('id', $id);
                            });
                        })->get();
                        if ($OrphanTagfilters->count() > 0) {
                            $data[$attribute->slug] = (new TagTransformer)->transformCollection($OrphanTagfilters->values(), $filterOptions);
                        }
                    } else {
                        if ($attribute->manual_position) {
                            $standardTagFilters = $standardTagFilters->where('type', 'attribute')->filter(function ($item) use ($id) {
                                $attributes = $item->attribute;
                                return $attributes->contains('id', $id);
                            })->sortBy(function ($filter) {
                                return $filter->attributePosition()
                                    ->pluck('position')
                                    ->first();
                            })->values();
                        }
                        $standardTagFilters = $standardTagFilters->where('type', 'attribute')->filter(function ($item) use ($id) {
                            $attributes = $item->attribute;
                            $products = $item->productTags->get();
                            $filteredProducts = $products->filter(function ($product) use ($id) {
                                // Check if $id exists in the pivot data
                                $pivotData = $product->pivot;
                                if (isset($pivotData) && $pivotData->attribute_id) {
                                    $pivotContainsId = isset($pivotData) && $pivotData->attribute_id && $pivotData->attribute_id == $id;
                                    return $pivotContainsId;
                                } else {
                                    return true;
                                }
                            });

                            // Return the filtered collection
                            return $attributes->contains('id', $id) && $filteredProducts->isNotEmpty();
                        })->values();
                        $data[$attribute->slug] = (new StandardTagTransformer)->transformCollection($standardTagFilters, $filterOptions);
                    }
                } else {
                    if ($attribute->manual_position) {
                        $sTag = $standardTagFilters->where('type', 'attribute')->filter(function ($item) use ($id) {
                            $attributes = $item->attribute;
                            $products = $item->productTags;

                            $filteredProducts = $products->filter(function ($product) use ($id) {
                                // Check if $id exists in the pivot data
                                $pivotData = $product->pivot;
                                if (isset($pivotData) && $pivotData->attribute_id) {
                                    $pivotContainsId = isset($pivotData) && $pivotData->attribute_id && $pivotData->attribute_id == $id;
                                    return $pivotContainsId;
                                } else {
                                    return true;
                                }
                            });

                            // Return the filtered collection
                            return $attributes->contains('id', $id) && $filteredProducts->isNotEmpty();
                        })
                            ->sortBy(function ($filter) {
                                return $filter->attributePosition()
                                    ->pluck('position')
                                    ->first();
                            })->values();
                    } else {
                        $sTag = $standardTagFilters->where('type', 'attribute')->filter(function ($item) use ($id) {
                            $attributes = $item->attribute;
                            $products = $item->productTags;
                            $filteredProducts = $products->filter(function ($product) use ($id) {
                                // Check if $id exists in the pivot data
                                $pivotData = $product->pivot;
                                if (isset($pivotData) && $pivotData->attribute_id) {
                                    $pivotContainsId = isset($pivotData) && $pivotData->attribute_id && $pivotData->attribute_id == $id;
                                    return $pivotContainsId;
                                } else {
                                    return true;
                                }
                            });

                            // Return the filtered collection
                            return $attributes->contains('id', $id) && $filteredProducts->isNotEmpty();
                        })->values();
                    }
                    if (count($sTag) > 0) {
                        $data[$attribute->slug] = (new StandardTagTransformer)->transformCollection($sTag, $filterOptions);
                    }
                }
            }

            if (!request()->input('business_uuid') && !request()->input('business_slug')) {
                // transforming brand tags
                if (count($standardTagFilters) > 0 && $standardTagFilters->where('type', 'brand')->count() > 0) {
                    $data['brand'] = (new StandardTagTransformer)->transformCollection($standardTagFilters->where('type', 'brand')->values(), $filterOptions);
                }
            }

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $data,
            ], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // industry tags
    public function getIndustryTags(Request $request, $module_id)
    {
        try {
            $industryTags = StandardTag::whereHas('businesses', function ($query) {
                $query->active()->when(request()->input('business'), function ($subQuery) {
                    $subQuery->where('slug', request()->input('business'))->orWhere('id', request()->input('business'));
                });
            })->has('productTags')->whereHas('levelTwo', function ($query) use ($module_id) {
                $query->where('L1', $module_id)->where('level_type', 4)
                    ->whereHas('standardTags.productTags');
            })->where('type', '!=', 'module')->active()->get();
            $options = [
                'withChildrens' => $request->input('withChildrens')
                    ? $request->withChildrens : false,
                'moduleId' => $module_id
            ];
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => (new StandardTagTransformer)->transformCollection($industryTags, $options),
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // module tags
    public function getModuleTags(Request $request)
    {
        try {
            $limit = $request->input('limit')
                ? $request->limit : \config()->get('settings.pagination_limit');
            $moduleTags = StandardTag::whereType('module')->where(function ($query) use ($request) {
                if ($request->input('keyword')) {
                    $query->where('name', 'like', '%' . $request->input('keyword') . '%');
                }
            })->where('status', 'active')->paginate($limit);
            $paginate = apiPagination($moduleTags, $limit);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => (new StandardTagTransformer)->transformCollection($moduleTags),
                'meta' => $paginate,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function verifyHierarchy(Request $request, $module_id)
    {
        try {
            $businessLevelTwoTags = [];
            if ($request->has('slug')) {
                $businessLevelTwoTags = StandardTag::whereHas('productTags.business', function ($query) {
                    $query->where('slug', request()->input('slug'));
                })->where(function ($query) use ($module_id, $request) {
                    $query->whereHas('LevelTwo', function ($subQuery) use ($module_id) {
                        $subQuery->where('L1', $module_id);
                    })->orWhereHas('tagHierarchies', function ($subQuery) use ($module_id) {
                        $subQuery->where('L1', $module_id)->where('level_type', 2);
                    });
                })->whereType('product')->active()->pluck('slug');
            }
            $tags =  array_filter([request()->input('level_two_tag'), request()->input('level_three_tag'), request()->input('level_four_tag')]);
            $hierachy = TagHierarchy::with(['levelTwo', 'levelThree', 'levelFour', 'standardTags'])
                ->where('L1', $module_id)
                ->where(function ($query) use ($businessLevelTwoTags) {
                    if (request()->input('level_four_tag')) {
                        $query->where('level_type', 4)
                            ->where(function ($subQuery)  use ($businessLevelTwoTags) {
                                $subQuery->whereHas('levelTwo', function ($subQuery) use ($businessLevelTwoTags) {
                                    if (request()->has('slug')) {
                                        $subQuery->whereIn('slug', $businessLevelTwoTags);
                                    } else {
                                        $subQuery->where('slug', request()->input('level_two_tag'));
                                    }
                                })->whereHas('levelThree', function ($subQuery) {
                                    $subQuery->where('slug', request()->input('level_three_tag'));
                                })->whereHas('standardTags', function ($subQuery) {
                                    $subQuery->where('slug', request()->input('level_four_tag'));
                                });
                            });
                    } else if (request()->input('level_three_tag')) {
                        $query->where(function ($subQuery) use ($businessLevelTwoTags) {
                            $subQuery->whereHas('levelTwo', function ($subQuery) use ($businessLevelTwoTags) {
                                if (request()->has('slug')) {
                                    $subQuery->whereIn('slug', $businessLevelTwoTags);
                                } else {
                                    $subQuery->where('slug', request()->input('level_two_tag'));
                                }
                            });
                        })->where(function ($subQuery) {
                            $subQuery->whereHas('standardTags', function ($subQuery) {
                                $subQuery->where('slug', request()->input('level_three_tag'));
                            })->orWhereHas('levelThree', function ($subQuery) {
                                $subQuery->where('slug', request()->input('level_three_tag'));
                            });
                        });
                    } else {
                        $query->whereHas('standardTags', function ($subQuery) {
                            $subQuery->where('slug', request()->input('level_two_tag'));
                        })->orWhereHas('levelTwo', function ($subQuery) {
                            $subQuery->where('slug', request()->input('level_two_tag'));
                        });
                    }
                })->whereHas('standardTags.productTags.business', function ($query) {
                    $query->whereStatus('active');
                    if (request()->business_slug) {
                        $query->whereSlug(request()->business_slug);
                    }
                })->firstOrFail();
            return response()->json([
                'data' => $hierachy,
                'status' => JsonResponse::HTTP_OK
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $hierachy;
    }


    private function customOrder($slug)
    {
        switch ($slug) {
            case 'bed':
            case 'beds':
                return 1;
            case 'bath':
            case 'baths':
                return 2;
            case 'square-foot':
            case 'square-feet':
                return 3;
            default:
                return 4;
        }
    }
}
