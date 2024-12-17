<?php

namespace Modules\Recipes\Http\Controllers\API;

use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use App\Traits\ModuleSessionManager;
use App\Transformers\ProductTransformer;
use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\HttpFoundation\JsonResponse;

class RecipeController extends Controller
{
    use StripeSubscription;

    public function __construct(protected StripeClient $stripeClient, private $filterParams = null)
    {
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
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
        $order_by = $request->filled('order') ? $request->input('order') : 'desc';
        $limit = $request->input('limit') ? $request->input('limit') : \config()->get('settings.pagination_limit');
        $options = [
            'withMinimumData' => true
        ];
        if (request()->filled('is_featured')) {
            $products = Product::with('user')->withCount('comments')
                ->whereRelation('standardTags', 'id', $moduleId)
                ->where('is_featured', Product::IS_FEATURED)
                ->where('status', Product::ACTIVE)
                ->where(function ($query) {
                    if (request()->input('keyword')) {
                        $keywords = explode(' ', request()->input('keyword'));
                        $query->search($keywords);
                    }
                })
                ->orderBy('created_at', $order_by)
                ->get();
        } else {
            $products = Product::with('user')->withCount('comments')->whereRelation('standardTags', 'id', $moduleId)->where(function ($query) use ($moduleId) {
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
                if (request()->input('favorite_users')) {
                    $user = auth('sanctum')->user();
                    if ($user) {
                        $query->whereHas('user.favoriteUsers', function ($subQuery) use ($user, $moduleId) {
                            $subQuery->where('user_id', $user->id)->where('module_id', $moduleId);
                        });
                    }
                }
                $query->when(request()->user_id, function ($query) {
                    $query->where('user_id', request()->user_id);
                });
                $query->when(request()->input('popular_product'), function ($subQuery) {
                    $subQuery->where('views_count', '>', 0);
                });
                if ($this->filterParams) {
                    $query->whereHas('standardTags', function ($query) {
                        $filters = Arr::flatten($this->filterParams);
                        if (count($filters) > 0)
                            $query->whereIn('id', Arr::flatten($this->filterParams));
                    });
                }
            })->where('status', 'active');
            if (request()->filled('latest')) {
                $products->latest();
            } else if (request()->input('popular_product')) {
                $products->orderBy('views_count', 'desc');
            } else {
                $products->orderBy('created_at', $order_by);
            }
            if ($this->filterParams) {
                $products = $this->filterCollection($products);
            }
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
        ModuleSessionManager::setModule('recipes');
        $request->merge([
            'user_id' => $request->user_id,
        ]);
        $product = Product::create($request->all());
        return $product;
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update($request, $moduleId, $productUuid)
    {
        ModuleSessionManager::setModule('recipes');
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
