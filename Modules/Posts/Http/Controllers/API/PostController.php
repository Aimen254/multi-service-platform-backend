<?php

namespace Modules\Posts\Http\Controllers\API;

use App\Models\User;
use App\Models\Product;
use App\Models\PublicProfile;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Transformers\ProductTransformer;
use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Traits\ModuleSessionManager;
use Illuminate\Support\Facades\Auth;



class PostController extends Controller
{
    use StripeSubscription;
    private $filterParams;

    public function __construct(protected StripeClient $stripeClient) {}

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
        $tagId  = $moduleId;
        if (request()->filled('is_featured')) {
            $module = StandardTag::find($moduleId);
            $userIds = $this->getSubscriptionCustomers('L1', $module->slug);
            $products = collect();
            //getting all users along with news
            $users = User::whereIn('stripe_customer_id', $userIds)->whereRelation('products', 'status', 'active')
                ->with(['products' => function ($query) use ($module, $request) {
                    $query->where('status', 'active')->whereRelation('standardTags', 'id', $module->id)
                        ->with('user')
                        ->limit(4);
                }])
                ->where('status', 'active')
                ->get();
            $users->each(function ($user, $key) use ($products) {
                $user->products->each(function ($product, $index) use ($products) {
                    $products->push($product);
                });
            });
        } else {
            $authUserId = Auth::id();
            $products = Product::with('user')
            ->withCount(['comments', 'wishList', 'views', 'likes','reposts'])
            ->whereHas('publicProfile', function ($query) use ($authUserId) {
                $query->where(function ($q) use ($authUserId) {
                    $q->where('user_id', $authUserId)
                      ->orWhere('is_public', true)
                      ->orWhereHas('followers', function ($q) {
                        $q->where('public_profile_follower.follower_public_profile_id', request()->input('profile_id'))
                          ->where('public_profile_follower.status', 'accepted');
                    });
                });
            })
            ->whereRelation('standardTags', 'id', $moduleId)
                ->where(function ($query) {
                    if (request()->input('public_profile_id')) {
                        $query->where('public_profile_id', request()->input('public_profile_id'));
                    }
                    if (request()->input('keyword')) {
                        $keywords = explode(' ', request()->input('keyword'));
                        $query->search($keywords);
                    }
                    if(request()->input('role') == 'business_owner'){
                        $query->where('user_id', request()->input('user_id'));
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
                    
                    if(!request()->input('disableStatusFilter')) {
                        $query->where('status', 'active');
                    }
                    if ($this->filterParams) {
                        $query->whereHas('standardTags', function ($query) {
                            $filters = Arr::flatten($this->filterParams);
                            if (count($filters) > 0)
                                $query->whereIn('id', Arr::flatten($this->filterParams));
                        });
                    }
                });
            if (request()->filled('latest')) {
                $products->latest();
            } else {
                $products->orderBy('created_at', $order_by);
            }
        }

        if ($this->filterParams) {
            $products = $this->filterCollection($products);
        }
        $products = $products->paginate($limit);
        $paginate = apiPagination($products, $limit);
        $products = (new ProductTransformer)->transformCollection($products, $options);

        $posts = collect($products)->map(function ($item) {
            return (object) $item;
        });

        $reposts=$this->withReposts($moduleId , $options);

        $combinedPosts = $posts->merge($reposts);

return response()->json([
    'status' => JsonResponse::HTTP_OK,
    'data' => $combinedPosts,
    'test'=>$reposts,
    'meta' => $paginate,
], JsonResponse::HTTP_OK);


    }


    private function withReposts($moduleId, $options)
    {
        $authUserId = Auth::id();
        $profileId = request()->input('public_profile_id');

        if (isset($profileId)) {
            $publicProfiles = PublicProfile::where('id', $profileId)->with('repostedProducts')->get();
        } else {
            $publicProfiles = PublicProfile::where('module_id', $moduleId)->with('repostedProducts')->get();
        }

        $repostedProductIds = collect();

        if ($publicProfiles) {
            $repostedProductIds = $publicProfiles->flatMap(function ($profile) {
                return $profile->repostedProducts->pluck('id');
            })->unique();
        }

        $productsWithFilter = Product::with('user')
            ->withCount(['comments', 'wishList', 'views', 'likes', 'reposts'])
            ->whereIn('id', $repostedProductIds)
            ->whereHas('publicProfile', function ($query) use ($authUserId) {
                $query->where(function ($q) use ($authUserId) {
                    $q->where('user_id', $authUserId)
                      ->orWhere('is_public', true)
                      ->orWhereHas('followers', function ($q) {
                        $q->where('public_profile_follower.follower_public_profile_id', request()->input('profile_id'))
                          ->where('public_profile_follower.status', 'accepted');
                      });
                });
            })
            ->whereRelation('standardTags', 'id', $moduleId)
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
                        })
                        ->where(function ($subQuery) {
                            $subQuery->whereHas('tagHierarchies', function ($subQuery) {
                                $subQuery->where('level_type', 2);
                            })
                            ->orWhereHas('levelTwo');
                        });
                    });
                }

                if (request()->input('level_three_tag')) {
                    $query->whereHas('standardTags', function ($query) {
                        $query->where(function ($query) {
                            $query->where('id', request()->input('level_three_tag'))
                                  ->orWhere('slug', request()->input('level_three_tag'));
                        })
                        ->where(function ($subQuery) {
                            $subQuery->whereHas('tagHierarchies', function ($subQuery) {
                                $subQuery->where('level_type', 3);
                            })
                            ->orWhereHas('levelThree');
                        });
                    });
                }

                if (request()->input('level_four_tag')) {
                    $query->whereHas('standardTags', function ($query) {
                        $query->where(function ($query) {
                            $query->where('id', request()->input('level_four_tag'))
                                  ->orWhere('slug', request()->input('level_four_tag'));
                        })
                        ->where(function ($subQuery) {
                            $subQuery->whereHas('tagHierarchies', function ($subQuery) {
                                $subQuery->where('level_type', 4);
                            })
                            ->orWhereHas('levelFour');
                        });
                    });
                }

                if (request()->input('disableStatusFilter')) {
                    $query->where('user_id', request()->input('user_id'));
                }

                if ($this->filterParams) {
                    $query->whereHas('standardTags', function ($query) {
                        $filters = Arr::flatten($this->filterParams);
                        if (count($filters) > 0) {
                            $query->whereIn('id', $filters);
                        }
                    });
                }
            })
            ->where('status', 'active')
            ->get();

        $productsWithFilter = (new ProductTransformer)->transformCollection($productsWithFilter, $options);

        $pivotData = $publicProfiles->flatMap(function ($profile) {
            return $profile->repostedProducts->map(function ($product) use ($profile) {
                return [
                    'profile' => $profile,
                    'product_id' => $product->id,
                    'user_id' => $product->pivot->user_id,
                    'public_profile_id' => $product->pivot->public_profile_id,
                    'created_at' => $product->pivot->created_at,
                    'updated_at' => $product->pivot->updated_at
                ];
            });
        });

        $reposts = collect($productsWithFilter)->map(function ($item) use ($pivotData) {
            $object = (object) $item;
            $object->is_reposted = true;

            $pivot = $pivotData->firstWhere('product_id', $item['id']);
            $object->pivot = $pivot ? $pivot : null;

            return $object;
        });

        return $reposts;
    }




    public function getStripeCustomerIds($module)
    {
        $L1 = $this->getSubscriptionCustomers('L1', $module);
        $L2 = $this->getSubscriptionCustomers('L2', $module);
        $L3 = $this->getSubscriptionCustomers('L3', $module);
        $userIds = Arr::collapse([$L1, $L2, $L3]);
        return $userIds;
    }



    public function store($request, $moduleId)
    {
        $isRepostable = filter_var($request->input('is_repostable'), FILTER_VALIDATE_BOOLEAN);
        $isShareable = filter_var($request->input('is_shareable'), FILTER_VALIDATE_BOOLEAN);
        ModuleSessionManager::setModule('posts');
         $request->merge([
            'is_repostable' => $isRepostable,
            'is_shareable' => $isShareable,
            ]);
        $product = Product::create($request->all());
        return $product;
    }

    public function update($request, $moduleId, $productUuid)
    {
        $isRepostable = filter_var($request->input('is_repostable'), FILTER_VALIDATE_BOOLEAN);
        $isShareable = filter_var($request->input('is_shareable'), FILTER_VALIDATE_BOOLEAN);
        $request->merge([
       'is_repostable' => $isRepostable,
       'is_shareable' => $isShareable,
       ]);

        ModuleSessionManager::setModule('posts');
        $product = Product::whereUuid($productUuid)->firstOrFail();
        $product->update($request->all());
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
