<?php

namespace Modules\Retail\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Business;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Http\JsonResponse;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Traits\ModuleSessionManager;
use Illuminate\Support\Facades\Route;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\Support\Renderable;
use Modules\Retail\Http\Requests\ProductRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{
    use StripeSubscription;
    public StripeClient $stripeClient;
    protected $busiessId;
    protected $business;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
        $this->business = getBusinessDetails(Route::current()->parameters['business_uuid'], 'retail');
        $this->busiessId = $this->business->id;
        $this->middleware('can:edit_products')->only('edit');
        $this->middleware('can:add_products')->only('create');
        $this->middleware('can:delete_products')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $maxPrice = Product::where('business_id', $this->busiessId)->max('price')
            ? Product::where('business_id', $this->busiessId)->max('price') : 1000;
        $minPrice = Product::where('business_id', $this->busiessId)->min('price')
            ? Product::where('business_id', $this->busiessId)->min('price') : 0;
        $barMinValue = request()->form && isset(request()->form['barMinValue']) ? request()->form['barMinValue'] : null;
        $barMaxValue = request()->form && isset(request()->form['barMaxValue']) ? request()->form['barMaxValue'] : null;
        $orderBy = request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc';
        $limit = \config()->get('settings.pagination_limit');
        $standardTags = StandardTag::where('type', '!=', 'module')->select(['id', 'name as text', 'slug'])
            ->whereHas('productTags', function ($query) {
                $query->where('business_id', $this->busiessId);
            })->get();
        $products = Product::with(['mainImage'])
            ->where(function ($query) use ($barMinValue, $barMaxValue) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->where('name', 'like', '%' . $keyword . '%');
                }
                if (request()->form && isset(request()->form['status'])) {
                    $query->where('status', request()->form['status']);
                }
                if (request()->form && isset(request()->form['barMinValue']) && isset(request()->form['barMaxValue'])) {
                    $query->whereBetween('price', [$barMinValue, $barMaxValue]);
                }
                if (request()->form && isset(request()->form['tag'])) {
                    $query->whereHas('standardTags', function ($subQuery) {
                        $subQuery->where('id', request()->form['tag']);
                    });
                }
            })
            ->where('business_id', $this->busiessId)
            ->orderBy('id', $orderBy)
            ->paginate($limit);

        return Inertia::render('Retail::Products/Index', [
            'productsList' => $products,
            'searchedKeyword' => request()->keyword,
            'orderBy' => request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc',
            'status' => request()->form && isset(request()->form['status']) ? request()->form['status'] : null,
            'maxPrice' => $maxPrice,
            'minPrice' => $minPrice,
            'barValueMin' => $barMinValue,
            'barValueMax' => $barMaxValue,
            'standardTags' => $standardTags,
            'tag' => request()->form && isset(request()->form['tag']) ? request()->form['tag'] : null
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($moduleId, $businessUuid)
    {
        //checking subscription permissions starts
        $business = Business::whereUuid($businessUuid)->firstOrfail();
        // $subscriptionPermission = $this->checkAllowedProducts($business);
        // if (!$subscriptionPermission) {
        //     flash('You can not add more products due to subscription limitations.', 'danger');
        //     return \redirect()->back();
        // }
        // checking subscription permissions ends
        $mediaSizes = \config()->get('retail.media.product');
        $levelTwoTags = null;
        $levelTwoTags = $this->business->standardTags()->whereHas('levelTwo')->select(['id', 'name as text', 'slug'])->get();
        if ($levelTwoTags->count() == 0) {
            $levelTwoTags = StandardTag::whereHas('levelTwo', function ($query) use ($moduleId) {
                $query->where('L1', $moduleId);
            })->select(['id', 'name as text', 'slug'])->get();
        }
        return Inertia::render('Retail::Products/Create', [
            'mediaSizes' => $mediaSizes,
            'levelTwoTags' => $levelTwoTags
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(ProductRequest $request, $moduleId, $busiessId)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule('retail');
            $request->merge([
                'business_id' => $this->busiessId,
            ]);
            Product::create($request->all());
            \flash('Product created successfully.', 'success');
            DB::commit();

            return \redirect()
                ->route('retail.dashboard.business.products.index', [$moduleId, $busiessId]);
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($moduleId, $busiessId, $uuid)
    {
        try {
            $product = Product::whereUuid($uuid)->firstOrFail();
            $productLevelTwoTag = $product->standardTags()->whereHas('levelTwo')->first();
            $levelTwoTags = $this->business->standardTags()->whereHas('levelTwo')->select(['id', 'name as text', 'slug'])->get();
            if ($levelTwoTags->count() == 0) {
                $levelTwoTags = StandardTag::whereHas('levelTwo', function ($query) use ($moduleId) {
                    $query->where('L1', $moduleId);
                })->select(['id', 'name as text', 'slug'])->get();
            }
            return Inertia::render('Retail::Products/Edit', [
                'product' => $product,
                'levelTwoTags' => $levelTwoTags,
                'productLevelTwoTag' => $productLevelTwoTag
            ]);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this product', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $uuid
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $moduleId, $busiessId, $uuid)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule('retail');
            $product = Product::whereUuid($uuid)->firstOrFail();
            $discount = null;
            if ($product->discount_value) {
                if ($product->discount_type == 'percentage') {
                    $discount = ($request->price * $product->discount_value) / 100;
                    $discount = $request->price - $discount;
                } else {
                    if ($request->price > $product->discount_value) {
                        $discount = $request->price - $product->discount_value;
                    }
                }
            }
            $request->merge([
                'discount_price' => numberFormat($discount)
            ]);
            $product->update($request->all());
            flash('Product basic information updated successfully.', 'success');
            DB::commit();
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this product.', 'danger');
            DB::rollBack();
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            DB::rollBack();
            return \redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($moduleId, $businessUuidd, $uuid, Request $request)
    {
        try {
            $currentPage = request()->query('page');
            $currentCount = request()->query('currentCount');
            $product = Product::where('uuid', $uuid)->firstOrfail();
            $product->delete();
            flash('Product deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('retail.dashboard.business.products.index', [$moduleId,$businessUuidd, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this product', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    /**
     * change status of specified resource from storage.
     *
     * @param  int $uuid, $id
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($moduleId, $businessUuidd, $uuid)
    {
        try {
            $product = Product::where('uuid', $uuid)->firstOrfail();
            $previousProductStatus = $product->status;
            if ($previousProductStatus == 'inactive') {
                //checking subscription permissions starts
                $business = Business::whereUuid($businessUuidd)->firstOrfail();
                // $subscriptionPermission = $this->checkAllowedProducts($business);
                // if (!$subscriptionPermission) {
                //     flash('You can not activate this product due to subscription limitations.', 'danger');
                //     return \redirect()->back();
                // }
                //checking subscription permissions ends
            }
            $check = ProductTagsLevelManager::checkProductTagsLevel($product);
            if (!$check) {
                flash('Tag error not resolved', 'danger');
            } else {
                if ($previousProductStatus == 'inactive') {
                    $product->status = 'active';
                } else {
                    $product->status = 'inactive';
                }
                $product->statusChanger();
                $product->saveQuietly();
                flash('Product status changed succesfully', 'success');
            }
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this product', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }


    /**
     * get of specified resource from storage.
     *
     * @param  int $module, $id
     * @return \Illuminate\Http\Response
     */
    public function getTags(Request $request, $moduleId, $busiessId, $tagId, $level)
    {
        try {
            $nextLevelTags = [];
            $productLevelThreeTag = null;
            $productLevelFourTag = null;
            if ($level == 2) {
                if (request()->product) {
                    $product = Product::findOrFail(request()->product);
                    $productLevelThreeTag = $product->standardTags()->whereHas('levelThree', function ($query) use ($tagId, $moduleId) {
                        $query->where('L1', $moduleId)->where('L2', $tagId);
                    })->select(['id', 'name as text', 'slug'])->first();
                } else {
                    $nextLevelTags = $this->business->standardTags()->where(function ($query) use ($tagId, $moduleId) {
                        $query->where('name', 'like', '%' . request()->keyword . '%');
                        $query->whereHas('tagHierarchies', function ($query) use ($tagId, $moduleId) {
                            $query->where('L2', $tagId)->where('L1', $moduleId);
                        })->orwhereHas('levelThree', function ($query) use ($tagId, $moduleId) {
                            $query->where('name', 'like', '%' . request()->keyword . '%');
                            $query->where('L2', $tagId)->where('L1', $moduleId);
                        });
                    })->select(['id', 'name as text', 'slug'])->paginate(50);
                    if ($nextLevelTags->count() == 0) {
                        $nextLevelTags = StandardTag::where('name', 'like', '%' . request()->keyword . '%')->whereHas('levelThree', function ($query) use ($tagId, $moduleId) {
                            $query->where('L1', $moduleId)->where('L2', $tagId);
                        })->select(['id', 'name as text', 'slug'])->paginate(50);
                    }
                }
            } else {
                if (request()->product) {
                    $product = Product::findOrFail(request()->product);
                    $productLevelFourTag = StandardTag::whereHas('productTags', function ($query) use ($moduleId, $tagId) {
                        $query->where('product_id', request()->product)->whereHas('standardTags', function ($subQuery) use ($moduleId, $tagId) {
                            $subQuery->whereIn('id', [$moduleId, $tagId, request()->levelTwoTag])->select('*', DB::raw('count(*) as total'))
                                ->having('total', '>=', 3);
                        });
                    })->whereHas('tagHierarchies', function ($query) use ($moduleId) {
                        $query->where('L1', $moduleId);
                        $query->where('level_type', 4);
                    })->select(['id', 'name as text', 'slug'])->get();
                } else {
                    $nextLevelTags = StandardTag::where('name', 'like', '%' . request()->keyword . '%')->whereHas('tagHierarchies', function ($query) use ($moduleId, $tagId) {
                        $query->where('L1', $moduleId)
                            ->where(function ($query) {
                                $query->where('L2', request()->levelTwoTag);
                            })->where(function ($query) use ($tagId) {
                                $query->where('L3',  $tagId);
                            })->where('level_type', 4);
                    })->select(['id', 'name as text', 'slug'])->get();
                }
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'tags' => $nextLevelTags,
                'productLevelThreeTag' => $productLevelThreeTag,
                'productLevelFourTag' => $productLevelFourTag
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
