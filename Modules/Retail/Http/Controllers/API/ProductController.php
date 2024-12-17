<?php

namespace Modules\Retail\Http\Controllers\API;

use App\Traits\ModuleSessionManager;
use Carbon\Carbon;
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
use App\Transformers\ProductTransformer;
use Illuminate\Contracts\Support\Renderable;

class ProductController extends Controller
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
    public function index($request, $module_id)
    {
        $deliveryFlag = $request->input('header_filter') && ($request->input('header_filter')) == 'delivery' ?  true : false;
        $page = $request->input('page');
        $options = [
            'withSecondaryImages' => true,
            'withVariants' => $request->input('withVariants'),
            'businessLevelThreeTags' => request()->input('businessLevelThreeTags'),
            'withDelivery' => $request->input('header_filter'),
        ];
        $this->filterParams = $request->input('filters');
        if (!is_array($this->filterParams)) {
            $this->filterParams = (array)json_decode($this->filterParams);
        }
        $order_by = $request->input('order') ? $request->input('order') : 'desc';

        $order_by_col = $request->input('order_by_col') ? $request->input('order_by_col') : 'name';
        $limit = $request->input('limit')
            ? $request->input('limit') : \config()->get('settings.pagination_limit');
        $tagId = request()->input('level_two_tag') ? request()->input('level_two_tag') : $module_id;
        if (request()->input('on_sale') || request()->input('featured') || request()->input('top_rated') || request()->input('weekly_trending')) {
            if (request()->input('on_sale')) {
                $onSale = Product::where(function ($query) use ($request) {
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
                })->whereStandardTag($tagId)->whereHas('mainImage')->whereHas('business', function ($query) {
                    $query->whereStatus('active');
                })->whereNotNull('discount_price')
                    ->where('discount_end_date', '>=', Carbon::now())
                    ->withCount(['reviews as reviews_avg' => function ($query) {
                        $query->select(DB::raw('avg(rating)'));
                    }])->take($limit)->get();
            }
            if (request()->input('featured')) {
                $featured = Product::where(function ($query) use ($request) {
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
                })->whereStandardTag($tagId)->whereHas('mainImage')->whereHas('business', function ($query) {
                    $query->whereStatus('active');
                })
                    ->where('is_featured', 1)
                    ->withCount(['reviews as reviews_avg' => function ($query) {
                        $query->select(DB::raw('avg(rating)'));
                    }])->take($limit)->get();
            }
            if (request()->input('top_rated')) {
                $topRated = Product::where(function ($query) use ($request) {
                    if ($request->input('level_three_tag')) {
                        $query->whereHas('standardTags', function ($query) use ($request) {
                            $query->where(function ($query) use ($request) {
                                $query->where('id', $request->input('level_three_tag'))
                                    ->orWhere('slug', $request->input('level_four_tag'));
                            })->whereHas('tagHierarchies', function ($subQuery) {
                                $subQuery->where('level_type', 3);
                            })->orwhereHas('levelThree');
                        });
                    }

                    if ($request->input('level_four_tag')) {
                        $query->whereHas('standardTags', function ($query) use ($request) {
                            $query->where(function ($query) use ($request) {
                                $query->where('id', $request->input('level_four_tag'))
                                    ->orWhere('slug', $request->input('level_four_tag'));
                            })->whereHas('tagHierarchies', function ($subQuery) {
                                $subQuery->where('level_type', 4);
                            })->orwhereHas('levelFour');
                        });
                    }
                })->whereStandardTag($tagId)->whereHas('mainImage')->whereHas('business', function ($query) {
                    $query->whereStatus('active');
                })
                    ->whereHas('reviews', function ($query) {
                        $query->where('rating', '>', 0);
                    })
                    ->withCount(['reviews as reviews_avg' => function ($query) {
                        $query->select(DB::raw('avg(rating)'));
                    }])
                    ->orderBy('reviews_avg', 'DESC')
                    ->take($limit)->get();
            }
            if (request()->input('weekly_trending')) {
                $weeklyTrendings = Product::where('status', 'active')->whereStandardTag($tagId)->whereHas('mainImage')->whereHas('business', function ($query) {
                    $query->where('status', 'active');
                })->when($deliveryFlag, function ($query) {
                    $query->where('is_deliverable', '!=', 0);
                    $query->whereHas('business.deliveryZone', function ($query) {
                        $query->where('delivery_type', '!=', 0);
                    });
                })
                    ->whereHas('orderItems.order', function ($query) {
                        $startDate = Carbon::now()->subDays(7);
                        $endDate = Carbon::now();
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    })->when(request()->input('level_three_tag'), function ($query) use ($request) {
                        $query->whereHas('standardTags', function ($query) use ($request) {
                            $query->where(function ($query) use ($request) {
                                $query->where('id', $request->input('level_three_tag'))
                                    ->orWhere('slug', $request->input('level_three_tag'));
                            })->whereHas('tagHierarchies', function ($subQuery) {
                                $subQuery->where('level_type', 3);
                            })->orwhereHas('levelThree');
                        });
                    })->withCount('orderItems as items_count')->orderBy('items_count', 'desc')->get();
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => [
                    'on_sale' => isset($onSale) ? (new ProductTransformer)->transformCollection($onSale, $options) : [],
                    'featured' => isset($featured) ? (new ProductTransformer)->transformCollection($featured, $options) : [],
                    'top_rated' => isset($topRated) ? (new ProductTransformer)->transformCollection($topRated, $options) : [],
                    'weekly_trending' => isset($weeklyTrendings) ? (new ProductTransformer)->transformCollection($weeklyTrendings, $options) : [],
                ],
            ], JsonResponse::HTTP_OK);
        } else {
            if ($page == '1' && !(request()->input('business_uuid') || request()->input('business_slug')) && empty($this->filterParams) && empty(request()->input('min_price') || \request()->input('max_price')) && !request()->filled('keyword')&& !request()->input('is_featured') && !request()->filled('role')) {
                $products = collect();
                //getting users with subscriptions with l1 to l3 level
                $module = StandardTag::find($module_id)->slug;
                $userIds = $this->getStripeCustomerIds($module);
                //getting all business along with products
                $businesses = Business::whereHas('businessOwner', function ($query) use ($userIds, $tagId, $request) {
                    $query->whereIn('stripe_customer_id', $userIds);
                })->whereHas('products', function ($query) {
                    $query->whereHas('standardTags', function ($query) {
                        $query->where('slug', 'retail');
                    });
                })->with(['products' => function ($query) use ($tagId, $request) {
                    $query->whereStandardTag($tagId)
                        ->when(request()->has('header_filter') && request()->input('header_filter') == 'delivery', function ($query) {
                            $query->where('is_deliverable', 1);
                        })->where(function ($query) use ($request) {

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
                $remainingProducts = Product::whereNotIn('id', $excludeProductIds)
                    ->whereStandardTag($tagId)
                    ->when(request()->has('header_filter') && request()->input('header_filter') == 'delivery', function ($query) {
                        $query->where('is_deliverable', 1);
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
                    ->whereRelation('business', 'status', 'active')
                    ->active()
                    ->withCount('reviews')
                    ->get();
                $remainingProducts->each(function ($product, $key) use ($products) {
                    $products->push($product);
                });
            } else {

                $myBusinesses = Business::where('owner_id', $request->user_id)
                ->where('status', 'active')
                ->pluck('id');

                $products = Product::whereIn('business_id',$myBusinesses)->whereHas('business.standardTags', function ($query) use ($tagId) {
                    $query->where('id', $tagId)->orWhere('slug', $tagId);
                })
                    ->when(request()->has('header_filter') && request()->input('header_filter') == 'delivery', function ($query) {
                        $query->where('is_deliverable', 1);
                    })->where(function ($query) use ($request) {
                        $query->active();
                        if ($request->input('keyword')) {
                            $keywords = explode(' ', $request->input('keyword'));
                            foreach ($keywords as $keyword) {
                                $query->where(function ($subQuery) use ($keyword) {
                                    $subQuery->where('name', 'like', '%' . $keyword . '%')
                                        ->orWhereHas('standardTags', function ($subQuery) use ($keyword) {
                                            $subQuery->where('name', $keyword);
                                        })->orWhere('description', 'like', '%' . $keyword . '%');
                                });
                            }
                        }
                        if ($request->input('is_featured')) {
                            $query->where('is_featured', 1);
                        }
                        if (($request->has('min_price') && $request->has('max_price')) || (isset($this->filterParams['price']) && count($this->filterParams['price']) == 2)) {
                            $query->whereBetween('price', [$request->input('min_price') ? $request->input('min_price') : $this->filterParams['price'][0], $request->input('max_price') ? $request->input('max_price') : $this->filterParams['price'][1]]);
                        }

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

                        if ($this->filterParams) {
                            if (!(request()->input('business_uuid') || request()->input('business_slug'))) {
                                $query->whereHas('standardTags', function ($query) {
                                    $filters = Arr::flatten($this->filterParams);
                                    if (count($filters) > 0)
                                        $query->whereIn('id', Arr::flatten($this->filterParams));
                                });
                            } else {
                                $filters = $this->getBrandAndSizeTagIds();
                                if (count($filters) > 0) {
                                    $query->whereHas('standardTags', function ($query) use ($filters) {
                                        $query->whereIn('id', $filters);
                                    });
                                }
                            }
                        }

                        if ($request->input('business_uuid') || $request->input('business_slug')) {
                            $query->whereHas('business', function ($query) use ($request) {
                                $query->where('uuid', $request->input('business_uuid'))->orWhere('slug', $request->input('business_slug'));
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
                    })->withCount('reviews');
                $products->orderBy($order_by_col, $order_by);
            }
            // // filter product again w.r.t exact filters
            if ($this->filterParams) {
                $products = $this->filterCollection($products);
            }
            // if ($request->filled('order') && $request->input('order') === 'desc' && !(request()->input('business_uuid') || request()->input('business_slug')) && $page == '1' && !request()->filled('keyword')) {
            //     $products = $products->orderBy('name', 'DESC');
            // }
            if ($request->filled('order') && $request->input('order') === 'asc' && !(request()->input('business_uuid') || request()->input('business_slug')) && $page == '1' && !request()->filled('keyword')) {
                $products = $products->sortBy('name');
            }
            $products = $products->paginate($limit);
            $paginate = apiPagination($products, $limit);
            $allProducts = (new ProductTransformer)->transformCollection($products, $options);
            // $allProducts = json_decode(json_encode($allProducts), true);
            // $allProducts = array_values($allProducts);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $allProducts,
                'meta' => $paginate,
            ], JsonResponse::HTTP_OK);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('retail::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store($request)
    {
        ModuleSessionManager::setModule('retail');
        $levelTwoTag = StandardTag::findOrFail($request->level_two_tag);
        $levelThreeTag  = StandardTag::findOrFail($request->level_three_tag);

        $cleanRequest = $request->except(['tags', 'removedTags']);
        $product = Product::create($cleanRequest);
        return $product;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($moduleId, $uuid)
    {
        $options = [
            'withDetails' => true,
            'withSecondaryImages' => true,
            'businessLevelThreeTags' => request()->input('businessLevelThreeTags'),
        ];
        $product = Product::whereUuid($uuid)
            ->whereHas('standardTags', function ($subQuery) use ($moduleId) {
                $subQuery->where('id', $moduleId)->orWhere('slug', $moduleId);
            })
            ->where('status', 'active')
            ->firstOrFail();
        $products = (new ProductTransformer)->transform($product, $options);
        return $products;
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('retail::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update($request, $moduleId, $productUuid)
    {
        ModuleSessionManager::setModule('retail');
        $product = Product::whereUuid($productUuid)->firstOrFail();
        $levelTwoTag = StandardTag::findOrFail($request->level_two_tag);
        $levelThreeTag  = StandardTag::findOrFail($request->level_three_tag);
        $levelFourTag  = StandardTag::findOrFail($request->level_four_tags);

        $cleanRequest = $request->except(['tags', 'removedTags']);
        $product->update($cleanRequest);

        return $product;
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

    public function removeBrandAndSizeTagIds()
    {
        $filters = $this->filterParams;
        unset($filters['size']);
        unset($filters['brands']);
        return Arr::flatten($filters);
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

    public function getStripeCustomerIds($module)
    {
        $L1 = $this->getSubscriptionCustomers('L1', $module);
        $L2 = $this->getSubscriptionCustomers('L2', $module);
        $L3 = $this->getSubscriptionCustomers('L3', $module);
        $userIds = Arr::collapse([$L1, $L2, $L3]);
        return $userIds;
    }
}
