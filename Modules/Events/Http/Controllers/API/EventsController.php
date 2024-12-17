<?php

namespace Modules\Events\Http\Controllers\API;

use stdClass;
use Carbon\Carbon;
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


class EventsController extends Controller
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
        $options = [
            'withMinimumData' => true
        ];

        $products = Product::with('user', 'events')->whereRelation('standardTags', 'id', $moduleId)->where(function ($query) use ($moduleId) {
            if (request()->input('keyword')) {
                $keywords = explode(' ', request()->input('keyword'));
                $query->search($keywords);
            }

            if ((request()->has('min_price') && request()->has('max_price')) || (isset($this->filterParams['price']) && count($this->filterParams['price']) == 2)) {
                $query->where(function ($query) {
                    $query->whereBetween('price', [
                        request()->input('min_price') ?? $this->filterParams['price'][0],
                        request()->input('max_price') ?? $this->filterParams['price'][1]
                    ])->orWhereBetween('max_price', [
                        request()->input('min_price') ?? $this->filterParams['price'][0],
                        request()->input('max_price') ?? $this->filterParams['price'][1]
                    ]);
                });
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

            if (request()->input('today_created')) {
                $query->whereDate('created_at', today());
            }
            if (request()->input('is_featured')) {
                $query->where('is_featured', 1);
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
                    });
                }
            }

            $query->when(request()->user_id, function ($query) {
                $query->where('user_id', request()->user_id);
            });
            $query->when(request()->input('popular_product'), function ($subQuery) {
                $subQuery->where('views_count', '>', 0);
            });
        })->when(!request()->input('disableStatusFilter'), function ($query) {
            $query->where('status', 'active')->whereEventDateNotPassed();
        })->when(request()->filled('latest'), function ($query) {
            $query->latest();
        })->when(request()->input('recently_viewed'), function ($query) {
            $user = auth('sanctum')->user();
            $query->whereRelation('views', 'user_id', $user?->id);
        })->when(request()->input('priceOrder'), function ($query) {
            $query->orderBy('price', request()->input('priceOrder'));
        })->when(!request()->filled('latest') && !request()->input('recently_viewed') && !request()->input('priceOrder'), function ($query) use ($order_by) {
            $query->orderBy('created_at', $order_by);
        });


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
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('events::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store($request, $moduleId)
    {
        ModuleSessionManager::setModule('events');
        $cleanRequest = $request->except(['tags', 'removedTags']);
        $cleanRequest['user_id'] = auth()->id();
        $product = Product::create($cleanRequest);
        $product->events()->create([
            'event_date' => $request->event_date,
            'event_location' => $request->event_location,
            'performer' => $request->performer,
            'away_team' => $request->away_team,
            'event_ticket' => $request->ticket_url,
        ]);
        return $product;
    }


    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('events::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('events::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update($request, $moduleId, $productUuid)
    {
        ModuleSessionManager::setModule('events');
        $product = Product::whereUuid($productUuid)->firstOrFail();
        $request->merge([
            'user_id' => auth()->id(),
        ]);
        $cleanRequest = $request->except(['tags', 'removedTags']);
        $product->update($cleanRequest);
        $product->events()->update([
            'event_date' => $request->event_date,
            'event_location' => $request->event_location,
            'performer' => $request->performer,
            'away_team' => $request->away_team,
            'event_ticket' => $request->ticket_url,
        ]);


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
