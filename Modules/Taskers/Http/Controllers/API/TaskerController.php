<?php

namespace Modules\Taskers\Http\Controllers\API;

use stdClass;
use App\Models\Tag;
use App\Models\User;
use App\Models\Product;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductTagsLevelManager;
use App\Transformers\ProductTransformer;
use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskerController extends Controller
{
    use StripeSubscription;

    public function __construct(protected StripeClient $stripeClient, private $filterParams = null)
    {
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
                ->orderBy('created_at', $order_by);
        } else {
            $products = Product::with(['user', 'views'])->withoutHiddenProducts()->when(request()->input('user_id'), function ($query) {
                $query->where('user_id', request()->user_id);
            })->whereRelation('standardTags', 'id', $moduleId)->where(function ($query) {
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
                })->when(!request()->filled('latest') && !request()->input('recently_viewed'), function ($query) use ($order_by) {
                    $query->orderBy('created_at', $order_by);
                });

            if (request()->input('recently_viewed')) {
                $user = auth('sanctum')->user();
                $products = $products->whereRelation('views', 'user_id', $user->id);
                $products = $products->get();
                $products = $products->sortByDesc('views_order')->values();
            }

            if (filter_var(request()->input('filtersTasker'), FILTER_VALIDATE_BOOLEAN)) {
                $products = $this->filterCollection($products, $moduleId);
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
        ModuleSessionManager::setModule('taskers');
        $request->merge([
            'user_id' => $request->user_id,
        ]);
        $product = Product::create($request->all());
        return $product;
    }

    public function update($request, $moduleId, $productUuid)
    {

        ModuleSessionManager::setModule('taskers');
        $product = Product::whereUuid($productUuid)->firstOrFail();
        $product->update($request->all());
        $this->assignTags($moduleId, $product);
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

    public function filterCollection($product, $module)
    {
         $module = StandardTag::where('id', $module)->orWhere('slug', $module)->first();
          $product = $product->get()->reject(function ($product, $key) use ($module) {
            $productLevelTwoTags = $product->standardTags()->whereHas('levelTwo', function ($query) use ($module) {
                $query->where('L1', $module?->id);
            })->whereRelation('users', 'id', request()->input('user_id'))->get();
            if ($productLevelTwoTags->count() > 0) {
                $product->standardTags()->whereHas('levelThree', function ($query) use ($productLevelTwoTags) {
                    $query->whereIn('L2', $productLevelTwoTags->pluck('id'));
                })->whereRelation('users', 'id', request()->input('user_id'))->get();
            } else {
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
        $product->standardTags()->sync($standardTags);
        if (count($removingAttributes) > 0) {
            ProductTagsLevelManager::priorityTwoTags($product, null, $removingAttributes, 'attribute');
        }
        ProductTagsLevelManager::priorityTwoTags($product);
    }
}
