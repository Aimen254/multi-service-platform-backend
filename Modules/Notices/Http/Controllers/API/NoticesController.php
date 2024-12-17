<?php

namespace Modules\Notices\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Traits\ModuleSessionManager;
use App\Transformers\ProductTransformer;
use Illuminate\Contracts\Support\Renderable;

class NoticesController extends Controller
{
    private $filterParams;
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
        $order_by_col = $request->filled('order_by_col') ? $request->input('order_by_col') : 'id';
        $limit = $request->input('limit') ? $request->input('limit') : \config()->get('settings.pagination_limit');

        $options = [
            'withMinimumData' => true
        ];

        $products = Product::when(request()->input('user_id'), function ($query) use ($moduleId) {
            $query->whereRelation('business', 'owner_id', request()->input('user_id'));
        }, function ($query) use ($moduleId) {
            $query->whereStandardTag($moduleId);
        })->whereRelation('standardTags', 'id', $moduleId)->whereRelation('business', 'status', 'active')->where(function ($query) {
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

                // // Filtering in orphan tags if thre is business_uuid
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
        })->when(!request()->input('disableStatusFilter'), function ($query) {
            $query->where('status', 'active');
        })->withCount('reviews')->when(request()->input('latest'), function ($query) {
            $query->latest();
        }, function ($query) use ($order_by_col, $order_by) {
            $query->orderBy($order_by_col, $order_by);
        });

        if ($this->filterParams) {
            $products = $this->filterCollection($products);
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
        return view('notices::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store($request, $moduleId)
    {
        ModuleSessionManager::setModule('notices');
        $request->merge([
            'business_id' => $request->business_id,
        ]);
        $product = Product::create($request->all());
        return $product;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('notices::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('notices::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update($request, $moduleId, $productUuid)
    {
        ModuleSessionManager::setModule('notices');
        $product = Product::whereUuid($productUuid)->firstOrFail();
        $product->update($request->all());
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
