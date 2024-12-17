<?php

namespace Modules\Services\Http\Controllers\API;

use App\Models\Product;
use App\Models\Business;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use App\Traits\ModuleSessionManager;
use App\Transformers\ProductTransformer;
use Illuminate\Contracts\Support\Renderable;

class ServicesController extends Controller
{
    use StripeSubscription;

    public function __construct(protected StripeClient $stripeClient, private $filterParams = null)
    {
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($request, $moduleId)
    {
        $this->filterParams = $request->input('filters');
        if (!is_array($this->filterParams)) {
            $this->filterParams = (array)json_decode($this->filterParams);
        }
        $order_by = $request->filled('order') ? $request->input('order') : 'desc';
        $limit = $request->input('limit') ? $request->input('limit') : \config()->get('settings.pagination_limit');
        $keyword = $request->input('keyword') ? $request->input('keyword') : '';
        $tagId = request()->input('level_two_tag') ? request()->input('level_two_tag') : $moduleId;
        $options = [
            'withMinimumData' => true
        ];
        if (request()->filled('is_featured')) {
            $module = StandardTag::find($moduleId);
            // $userIds = $this->getSubscriptionCustomers('L1', $module->slug);
            // $products = collect();
            // $businesses = Business::whereHas('businessOwner', function ($query) use ($userIds, $tagId, $request) {
            //     $query->whereIn('stripe_customer_id', $userIds);
            // })->whereHas('products')->with(['products' => function ($query) use ($tagId, $request) {
            //     $query->withoutHiddenProducts();
            //     $query->whereStandardTag($tagId)
            //         ->where(function ($query) use ($request) {
            //             if ($request->input('level_three_tag')) {
            //                 $query->whereHas('standardTags', function ($query) use ($request) {
            //                     $query->where(function ($query) use ($request) {
            //                         $query->where('id', $request->input('level_three_tag'))
            //                             ->orWhere('slug', $request->input('level_three_tag'));
            //                     })->where(function ($subQuery) {
            //                         $subQuery->whereHas('tagHierarchies', function ($subQuery) {
            //                             $subQuery->where('level_type', 3);
            //                         })->orWhereHas('levelThree');
            //                     });
            //                 });
            //             }

            //             if ($request->input('level_four_tag')) {
            //                 $query->whereHas('standardTags', function ($query) use ($request) {
            //                     $query->where(function ($query) use ($request) {
            //                         $query->where('id', $request->input('level_four_tag'))
            //                             ->orWhere('slug', $request->input('level_four_tag'));
            //                     })->where(function ($subQuery) {
            //                         $subQuery->whereHas('tagHierarchies', function ($subQuery) {
            //                             $subQuery->where('level_type', 4);
            //                         })->orWhereHas('levelFour');
            //                     });
            //                 });
            //             }
            //         })
            //         ->active()
            //         ->limit(4);
            // }])
            //     ->where('businesses.status', 'active')
            //     ->get();

            // //collection all products from businesses
            // $businesses->each(function ($business, $key) use ($products) {
            //     $business->products->each(function ($product, $index) use ($products) {
            //         $products->push($product);
            //     });
            // });

            $products = Product::withoutHiddenProducts()->whereRelation('standardTags', 'id', $moduleId)
                ->when(request()->input('keyword'), function ($query) use ($keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('name', 'like', '%' .  $keyword . '%')
                            ->orWhere('description', 'like', '%' .  $keyword . '%');
                    });
                })
                ->orderBy('created_at', $order_by)
                ->where('is_featured', Product::IS_FEATURED)->whereRelation('business', 'status', 'active')
                ->active();
        } else {
            $products = Product::with('views')->when(request()->input('user_id'), function ($query) {
                $query->whereRelation('business', 'owner_id', request()->input('user_id'));
            })->withoutHiddenProducts()->whereRelation('standardTags', 'id', $moduleId)->whereRelation('business', 'status', 'active')
                ->where(function ($query) {
                    if (request()->input('keyword')) {
                        $keywords = explode(' ', request()->input('keyword'));
                        $query->search($keywords);
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
                    if (request()->input('business_uuid') || request()->input('business_slug')) {
                        $query->whereHas('business', function ($query) {
                            $query->where('uuid', request()->input('business_uuid'))->orWhere('slug', request()->input('business_slug'));
                        });
                    }
                    if ($this->filterParams) {
                        $query->whereHas('standardTags', function ($query) {
                            $filters = Arr::flatten($this->filterParams);
                            if (count($filters) > 0)
                                $query->whereIn('id', Arr::flatten($this->filterParams));
                        });
                    }
                })->where('status', 'active')
                ->when(request()->filled('latest'), function ($query) {
                    $query->latest();
                })->when(request()->input('recently_viewed'), function ($query) {
                    $user = auth('sanctum')->user();
                    $query->whereRelation('views', 'user_id', $user?->id);
                })->when(!request()->filled('latest') && !request()->input('recently_viewed'), function ($query) use ($order_by) {
                    $query->orderBy('created_at', $order_by);
                });
            if ($this->filterParams) {
                $products = $this->filterCollection($products);
            }
        }
        if (request()->input('recently_viewed')) {
            $products = $products->get();
            $products = $products->sortByDesc('views_order')->values();
        }
        $products = $products->paginate($limit);

        $paginate = apiPagination($products, $limit);
        $products = (new ProductTransformer)->transformCollection($products, $options);
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'data' => $products,
            'meta' => $paginate,
        ], JsonResponse::HTTP_OK);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($request, $moduleId)
    {
        ModuleSessionManager::setModule('services');
        $request->merge([
            'business_id' => $request->business_id,
        ]);
        $product = Product::create($request->all());
        return $product;
    }


    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request  $request
     * @param int $moduleId
     * @param uuid  $productUuid
     * @return Renderable
     */
    public function update($request, $moduleId, $productUuid)
    {
        ModuleSessionManager::setModule('services');
        $product = Product::whereUuid($productUuid)->firstOrFail();
        $product->update($request->all());
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
}
