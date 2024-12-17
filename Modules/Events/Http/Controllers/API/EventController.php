<?php

namespace Modules\Events\Http\Controllers\API;

use App\Models\Product;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Stripe\StripeClient;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use App\Transformers\ProductTransformer;
use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\HttpFoundation\Response;

class EventController extends Controller
{
    // use StripeSubscription;
    // public StripeClient $stripeClient;
    private $filterParams;

    // public function __construct(StripeClient $stripeClient)
    // {
    //     $this->stripeClient = $stripeClient;
    // }

    /**
     * Display a listing of the resource.
     * @param $request
     * @param $moduleId
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
        })->where('status', 'active')->when(request()->filled('latest'), function ($query) {
            $query->latest();
        })->when(request()->input('recently_viewed'), function ($query) {
            $user = auth('sanctum')->user();
            $query->whereRelation('views', 'user_id', $user?->id);
        })->when(!request()->filled('latest') && !request()->input('recently_viewed'), function ($query) use ($order_by) {
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
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, $id)
    {
        //
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
}
