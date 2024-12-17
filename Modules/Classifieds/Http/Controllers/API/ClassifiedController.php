<?php

namespace Modules\Classifieds\Http\Controllers\API;

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
use Illuminate\Support\Facades\DB;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductTagsLevelManager;
use App\Transformers\ProductTransformer;
use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClassifiedController extends Controller
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
            // $module = StandardTag::find($moduleId);
            // $userIds = $this->getSubscriptionCustomers('L1', $module->slug);
            // $products = collect();
            // //getting all users along with classifieds
            // $users = User::whereIn('stripe_customer_id', $userIds)->whereRelation('products', 'status', 'active')
            //     ->with(['products' => function ($query) use ($module, $request) {
            //         $query->withoutHiddenProducts()->where('status', 'active')->whereRelation('standardTags', 'id', $module->id)
            //             ->with('user')
            //             ->limit(4);
            //     }])
            //     ->where('status', 'active')
            //     ->get();
            // $users->each(function ($user, $key) use ($products) {
            //     $user->products->each(function ($product, $index) use ($products) {
            //         $products->push($product);
            //     });
            // });

            $products = Product::with('user')->withCount('comments')
                ->withoutHiddenProducts()
                ->where('is_featured', Product::IS_FEATURED)
                ->whereRelation('standardTags', 'id', $moduleId)
                ->where('status', 'active')
                ->where(function ($query) {
                    if (request()->input('keyword')) {
                        $keywords = explode(' ', request()->input('keyword'));
                        $query->search($keywords);
                    }
                })
                ->orderBy('created_at', $order_by)
                ->get();
        } else {
            $products = Product::with('user')->withCount('comments')->withoutHiddenProducts()->when(request()->input('user_id'), function ($query) {
                $query->where('user_id', request()->user_id);
            })->whereRelation('standardTags', 'id', $moduleId)->where(function ($query) use ($moduleId){
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

                if (request()->input('favorite_users')) {
                    $user = auth('sanctum')->user();
                    if ($user) {
                        $query->whereHas('user.favoriteUsers', function ($subQuery) use ($user, $moduleId) {
                            $subQuery->where('user_id', $user->id)->where('module_id', $moduleId);
                        })->where('status', 'active');
                    }
                }
            });

            if (!request()->input('disableStatusFilter')) {
                $products->active();
            }

            if (request()->input('recently_viewed')) {
                $user = auth('sanctum')->user();
                $products = $products->whereRelation('views', 'user_id', $user?->id);
                $products = $products->get();
                $products = $products->sortByDesc('views_order')->values();
            } else if (request()->filled('latest')) {
                $products->latest();
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
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule(Product::MODULE_MARKETPLACE);
            $request->merge([
                'user_id' => $request->user_id,
            ]);
            $cleanRequest = $request->except(['tags', 'removedTags']);
            $product = Product::create($cleanRequest);
            // attaching condition attribute
            $product->standardTags()->syncWithoutDetaching($request->condition);
            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update($request, $moduleId, $productUuid)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule(Product::MODULE_MARKETPLACE);
            $product = Product::whereUuid($productUuid)->firstOrFail();
            $cleanRequest = $request->except(['tags', 'removedTags']);
            $product->update($cleanRequest);

            // remove condition attribute
            if ($request->removed_tag) {
                $product->standardTags()->detach($request->removed_tag);
            }

            $this->assignTags($moduleId, $product);

            // attach condition attribute
            $product->standardTags()->syncWithoutDetaching($request->condition);

            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
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
