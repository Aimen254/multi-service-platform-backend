<?php

namespace Modules\Boats\Http\Controllers\API;

use stdClass;
use App\Models\Tag;
use App\Models\Product;
use App\Models\Business;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductTagsLevelManager;
use App\Transformers\ProductTransformer;
use App\Transformers\VehicleTransformer;
use Illuminate\Contracts\Support\Renderable;
use Modules\Boats\Http\Requests\BoatRequest;
use Modules\Automotive\Entities\ProductAutomotive;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BoatController extends Controller
{
    use StripeSubscription;
    public StripeClient $stripeClient;
    private $filterParams;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request, $moduleId)
    {
        $this->filterParams = $request->input('filters');
        if (!is_array($this->filterParams)) {
            $this->filterParams = (array)json_decode($this->filterParams);
        }
        $order_by_col = $request->filled('order_by_col') ? $request->input('order_by_col') : 'name';
        $order_by = $request->filled('order') ? $request->input('order') : 'desc';
        $limit = $request->input('limit') ? $request->input('limit') : \config()->get('settings.pagination_limit');
        $tagId = request()->input('level_two_tag') ? request()->input('level_two_tag') : $moduleId;
        if (request()->input('page') == '1' && !(request()->input('business_uuid') || request()->input('business_slug')) && empty($this->filterParams) && empty(request()->input('min_price') || \request()->input('max_price')) && !request()->filled('keyword') && !request()->input('from') && !request()->input('to') && !request()->input('user_id') && !request()->input('is_featured')) {
            $products = collect();
            //getting users with subscriptions with l1 to l3 level
            $module = StandardTag::find($moduleId)->slug;
            $userIds = $this->getStripeCustomerIds($module);
            //getting all business along with products
            $businesses = Business::whereHas('businessOwner', function ($query) use ($userIds, $tagId, $request) {
                $query->whereIn('stripe_customer_id', $userIds);
            })->whereHas('products')->with(['products' => function ($query) use ($tagId, $request) {
                $query->whereStandardTag($tagId)
                    ->with('vehicle')
                    ->when(request()->has('header_filter'), function ($query) {
                        $query->whereRelation('vehicle', 'type', request('header_filter'));
                    })
                    ->where(function ($query) use ($request) {
                        if ($request->input('level_three_tag')) {
                            $query->whereHas('standardTags', function ($query) use ($request) {
                                $query->where(function ($query) use ($request) {
                                    $query->where('id', $request->input('level_three_tag'))
                                        ->orWhere('slug', $request->input('level_three_tag'));
                                })->where(function ($subQuery) {
                                    $subQuery->whereHas('tagHierarchies', function ($subQuery) {
                                        $subQuery->where('level_type', 3);
                                    })->orWhereHas('levelThree');
                                });
                            });
                        }

                        if ($request->input('level_four_tag')) {
                            $query->whereHas('standardTags', function ($query) use ($request) {
                                $query->where(function ($query) use ($request) {
                                    $query->where('id', $request->input('level_four_tag'))
                                        ->orWhere('slug', $request->input('level_four_tag'));
                                })->where(function ($subQuery) {
                                    $subQuery->whereHas('tagHierarchies', function ($subQuery) {
                                        $subQuery->where('level_type', 4);
                                    })->orWhereHas('levelFour');
                                });
                            });
                        }
                    })
                    ->active()
                    ->limit(4);
            }])
                ->select(['businesses.*', 'users.stripe_customer_id'])
                ->join('users', 'businesses.owner_id', '=', 'users.id')
                ->orderByRaw(DB::raw("FIND_IN_SET(users.stripe_customer_id, '" . implode(',', $userIds) . "')"))
                ->where('businesses.status', 'active')
                ->get();
            //collection all products from businesses
            $businesses->each(function ($business, $key) use ($products) {
                $business->products->each(function ($product, $index) use ($products) {
                    $products->push($product);
                });
            });
            //products ids to exclude
            $excludeProductIds = $products->pluck('id')->toArray();
            // merge all products if the featured products are less then 36
            $remainingProducts = Product::with('vehicle')->whereNotIn('id', $excludeProductIds)
                ->whereStandardTag($tagId)
                ->when(request()->has('header_filter'), function ($query) {
                    $query->whereRelation('vehicle', 'type', request('header_filter'));
                })
                ->where(function ($query) use ($request) {
                    if ($request->input('level_three_tag')) {
                        $query->whereHas('standardTags', function ($query) use ($request) {
                            $query->where(function ($query) use ($request) {
                                $query->where('id', $request->input('level_three_tag'))
                                    ->orWhere('slug', $request->input('level_three_tag'));
                            })->where(function ($subQuery) {
                                $subQuery->whereHas('tagHierarchies', function ($subQuery) {
                                    $subQuery->where('level_type', 3);
                                })->orWhereHas('levelThree');
                            });
                        });
                    }
                    if ($request->input('level_four_tag')) {
                        $query->whereHas('standardTags', function ($query) use ($request) {
                            $query->where(function ($query) use ($request) {
                                $query->where('id', $request->input('level_four_tag'))
                                    ->orWhere('slug', $request->input('level_four_tag'));
                            })->where(function ($subQuery) {
                                $subQuery->whereHas('tagHierarchies', function ($subQuery) {
                                    $subQuery->where('level_type', 4);
                                })->orWhereHas('levelFour');
                            });
                        });
                    }
                })
                ->active()
                ->withCount('reviews')
                ->get();
            $remainingProducts->each(function ($product, $key) use ($products) {
                $products->push($product);
            });
        } else {
            $products = Product::active()->with('vehicle.model', 'vehicle.maker')->when(request()->input('user_id'), function ($query) use ($moduleId) {
                $query->whereHas('standardTags', function ($subQuery) use ($moduleId) {
                    $subQuery->where('id', $moduleId)->orWhere('slug', $moduleId);
                })->when(request()->input('is_business'), function ($query) use ($moduleId) {
                    $query->whereHas('business', function ($subQuery) use ($moduleId) {
                        $subQuery->where('owner_id', request()->user_id)->where('status', 'active');
                    });
                }, function ($query) {
                    $query->where('user_id', request()->user_id);
                });
            }, function ($query) use ($moduleId) {
                $query->whereStandardTag($moduleId);
            })->where(function ($query) {
                if (request()->input('keyword')) {
                    $keywords = explode(' ', request()->input('keyword'));
                    $query->search($keywords);
                }

                if ((request()->has('min_price') && request()->has('max_price')) || (isset($this->filterParams['price']) && count($this->filterParams['price']) == 2)) {
                    $query->whereBetween('price', [request()->input('min_price') ? request()->input('min_price') : $this->filterParams['price'][0], request()->input('max_price') ? request()->input('max_price') : $this->filterParams['price'][1]]);
                }

                if (request()->input('level_two_tag')) {
                    $query->whereHas('standardTags', function ($query) {
                        $query->where(function ($query) {
                            $query->where('id', request()->input('level_two_tag'))
                                ->orWhere('slug', request()->input('level_two_tag'));
                        })->where(function ($subQuery) {
                            $subQuery->whereHas('tagHierarchies', function ($subQuery) {
                                $subQuery->where('level_type', 2);
                            })->orWhereHas('levelTwo');
                        });
                    });
                }

                if (request()->input('level_three_tag')) {
                    $query->whereHas('standardTags', function ($query) {
                        $query->where(function ($query) {
                            $query->where('id', request()->input('level_three_tag'))
                                ->orWhere('slug', request()->input('level_three_tag'));
                        })->where(function ($subQuery) {
                            $subQuery->whereHas('tagHierarchies', function ($subQuery) {
                                $subQuery->where('level_type', 3);
                            })->orWhereHas('levelThree');
                        });
                    });
                }

                if (request()->input('level_four_tag')) {
                    $query->whereHas('standardTags', function ($query) {
                        $query->where(function ($query) {
                            $query->where('id', request()->input('level_four_tag'))
                                ->orWhere('slug', request()->input('level_four_tag'));
                        })->where(function ($subQuery) {
                            $subQuery->whereHas('tagHierarchies', function ($subQuery) {
                                $subQuery->where('level_type', 4);
                            })->orWhereHas('levelFour');
                        });
                    });
                }

                if ($this->filterParams) {
                    if (!(request()->input('business_uuid') || request()->input('business_slug'))) {
                        $query->whereHas('standardTags', function ($query) {
                            $filters = Arr::flatten($this->filterParams);
                            if (count($filters) > 0)
                                $query->whereIn('id', Arr::flatten($this->filterParams));
                        });
                    }
                }

                if (request()->input('is_featured')) {
                    $query->where('is_featured', 1);
                }

                if (request()->input('top_rated')) {
                    $query->whereHas('reviews', function ($query) {
                        $query->where('rating', '>', 0);
                    })
                        ->withCount(['reviews as reviews_avg' => function ($query) {
                            $query->select(DB::raw('avg(rating)'));
                        }])
                        ->orderBy('reviews_avg', 'DESC');
                }

                if (request()->filled('header_filter')) {
                    $query->whereRelation('vehicle', 'type', request()->input('header_filter'));
                }
                if (request()->input('business_uuid') || request()->input('business_slug')) {
                    $query->whereHas('business', function ($query) {
                        $query->where('uuid', request()->input('business_uuid'))->orWhere('slug', request()->input('business_slug'));
                    });

                    // Filtering in orphan tags if thre is business_uuid
                    if ($this->filterParams) {
                        $query->whereHas('tags', function ($query) {
                            $filters = $this->removeBrandAndSizeTagIds();
                            if (count($filters) > 0) {
                                $query->whereHas('standardTags_', function ($query) {
                                    $query->whereType('attribute');
                                })->whereIn('id', Arr::flatten($filters));
                            }
                        });
                    }
                }
            })
                ->when(request()->input('from') && request()->input('to'), function ($query) {
                    $query->whereHas('vehicle', function ($innerQuery) {
                        $innerQuery->whereBetween('year', [request()->input('from'), request()->input('to')]);
                    });
                })
                ->when(request()->input('is_garage'), function ($query) {
                    $query->whereDoesntHave('business');
                    $query->where('status', '!=', 'sold');
                })->when(request()->input('is_sold'), function ($query) {
                    $query->where('status', 'sold');
                })
                ->when(!request()->input('disableStatusFilter'), function ($query) {
                    $query->where('status', 'active');
                })->withCount('reviews')->orderBy($order_by_col, $order_by);
        }
        if ($this->filterParams) {
            $products = $this->filterCollection($products);
        }
        $products = $products->paginate($limit);
        $paginate = apiPagination($products, $limit);
        $products = (new ProductTransformer)->transformCollection($products);
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'data' => $products,
            'meta' => $paginate,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('boats::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store($request, $moduleId)
    {
        ModuleSessionManager::setModule('boats');
        $levelThreeTag = StandardTag::findOrFail($request->level_three_tag);
        $levelFourTag = StandardTag::findOrFail($request->level_four_tags);
        $request->merge([
            'user_id' => $request->user_id,
            'name' => $levelThreeTag->name . ' ' . $levelFourTag->name,
            'maker_id' => $levelThreeTag->id,
            'model_id' => $levelFourTag->id
        ]);
        $cleanRequest = $request->except(['tags', 'removedTags']);
        $product = Product::create($cleanRequest);
        $product->vehicle()->create($request->all());
        $this->assignTags($moduleId, $product);
        return $product;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($moduleId, $uuid)
    {
        $product = Product::with('vehicle.model', 'vehicle.maker')
            ->whereUuid($uuid)
            ->whereHas('standardTags', function ($subQuery) use ($moduleId) {
                $subQuery->where('id', $moduleId)->orWhere('slug', $moduleId);
            })
            ->where('status', 'active')
            ->firstOrFail();
        $products = (new ProductTransformer)->transform($product);
        return $products;
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('boats::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update($request, $moduleId, $productUuid)
    {
        ModuleSessionManager::setModule('boats');
        $product = Product::whereUuid($productUuid)->firstOrFail();
        $levelThreeTag = StandardTag::findOrFail($request->level_three_tag);
        $levelFourTag = StandardTag::findOrFail($request->level_four_tags);
        $request->merge([
            'name' => $levelThreeTag->name . ' ' . $levelFourTag->name,
            'maker_id' => $levelThreeTag->id,
            'model_id' => $levelFourTag->id
        ]);
        $cleanRequest = $request->except(['tags', 'removedTags']);
        $product->update($cleanRequest);
        $product->vehicle()->update([
            'type' => $request->type,
            'trim' => $request->trim,
            'year' => $request->year,
            'mpg' => $request->mpg,
            'stock_no' => $request->stock_no,
            'vin' => $request->vin,
            'sellers_notes' => $request->sellers_notes,
            'mileage' => $request->mileage,
            'maker_id' => $request->maker_id,
            'model_id' => $request->model_id
        ]);
        return $product;
    }

    public function filterCollection($product)
    {
        $product = $product->get()->reject(function ($product, $key) {
            $standardTag = $product->standardTags()->pluck('id')->toArray();
            $tags = $product->tags()->pluck('id')->toArray();
            if (request()->input('business_uuid') || request()->input('business_slug')) {
                //checking tags in orphan tag but not size and brands
                $data = $this->removeBrandAndSizeTagIds();
                // Checking in orphan tags
                if (count($data) > 0 && $this->tagIsNotInProduct($data, $tags))
                    return true;
                // Checking brands and size from standard tags
                $data = $this->getBrandAndSizeTagIds();

                if ($data > 0 && $this->tagIsNotInProduct($data, $standardTag))
                    return true;
            } else {
                $data = Arr::flatten($this->filterParams);
                if (count($data) > 0 && $this->tagIsNotInProduct($data, $standardTag))
                    return true;
            }
        })->values();
        return $product;
    }

    public function tagIsNotInProduct($inputTags, $productTags)
    {
        // Checking if all request tags are attached to product tags
        $allTagsInProduct = array_intersect($productTags, $inputTags);
        if (count($inputTags) == count($allTagsInProduct)) {
            return false;
        }
        return true;
    }

    public function removeBrandAndSizeTagIds()
    {
        $filters = $this->filterParams;
        unset($filters['size']);
        unset($filters['brands']);
        return Arr::flatten($filters);
    }

    public function getBrandAndSizeTagIds()
    {
        $filters = [];
        if (isset($this->filterParams['size']) || isset($this->filterParams['brands'])) {
            if (isset($this->filterParams['size']))
                array_push($filters, $this->filterParams['size']);
            if (isset($this->filterParams['brands']))
                array_push($filters, $this->filterParams['brands']);
        }

        return Arr::flatten($filters);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
    public function assignTags($moduleId, $product)
    {
        $standardTags = request()->input('selectedTags') ? json_decode(request()->input('selectedTags')) : [];
        if (request()->input('year')) {
            $tag =
                Tag::updateOrCreate([
                    'slug' => orphanTagSlug(trim(request()->year))
                ], [
                    'name' => request()->year,
                    'type' => 'product',
                ]);

            $standardTag = StandardTag::where('name', $tag->name)->first();
            if ($standardTag) {
                $tag->update([
                    'priority' => $standardTag->priority,
                    'is_show' => \true
                ]);
                $product->tags()->syncWithoutDetaching($tag->id);
                $item = new stdClass();
                $item->value = $standardTag->id;
                array_push($standardTags, $item);
            }
        }
        $allstandardTagIds = [];
        foreach ($standardTags as $sTag) {
            array_push($allstandardTagIds, $sTag->value);
        }
        $standardTags = Arr::collapse([$product->standardTags()->where('type', '!=', 'attribute')->pluck('id')->toArray(), $allstandardTagIds]);
        $existedAttributeTags = $product->standardTags()->whereType('attribute')->pluck('id')->toArray();
        $removingAttributes = array_diff($existedAttributeTags, $allstandardTagIds);
        $product->standardTags()->syncWithoutDetaching($standardTags);
        if (count($removingAttributes) > 0) {
            ProductTagsLevelManager::priorityTwoTags($product, null, $removingAttributes, 'attribute');
        }
        ProductTagsLevelManager::priorityTwoTags($product);
    }

    public function getStripeCustomerIds($module)
    {
        $L1 = $this->getSubscriptionCustomers('L1', $module);
        $L2 = $this->getSubscriptionCustomers('L2', $module);
        $L3 = $this->getSubscriptionCustomers('L3', $module);
        $userIds = Arr::collapse([$L1, $L2, $L3]);
        return $userIds;
    }
}
