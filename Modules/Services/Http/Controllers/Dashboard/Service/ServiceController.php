<?php

namespace Modules\Services\Http\Controllers\Dashboard\Service;

use Exception;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Business;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Traits\ModuleSessionManager;
use Illuminate\Support\Facades\Route;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Contracts\Support\Renderable;
use Modules\Services\Http\Requests\ServiceRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;
use App\Traits\ProductPriorityManager;

class ServiceController extends Controller
{
    use StripeSubscription;
    public StripeClient $stripeClient;
    protected $businessId;
    protected $business;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
        $this->business = Business::where('uuid', Route::current()->parameters['business_uuid'])->first();
        $this->businessId = $this->business->id;
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
        $maxPrice = Product::where('business_id', $this->businessId)->max('price')
            ? Product::where('business_id', $this->businessId)->max('price') : 1000;
        $minPrice = Product::where('business_id', $this->businessId)->min('price')
            ? Product::where('business_id', $this->businessId)->min('price') : 0;
        $barMinValue = request()->form && isset(request()->form['barMinValue']) ? request()->form['barMinValue'] : null;
        $barMaxValue = request()->form && isset(request()->form['barMaxValue']) ? request()->form['barMaxValue'] : null;
        $orderBy = request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc';
        $limit = \config()->get('settings.pagination_limit');
        $standardTags = StandardTag::where('type', '!=', 'module')->select(['id', 'name as text', 'slug'])
            ->whereHas('productTags', function ($query) {
                $query->where('business_id', $this->businessId);
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
            ->where('business_id', $this->businessId)
            ->withCount('comments')
            ->orderBy('id', $orderBy)
            ->paginate($limit);

        return Inertia::render('Services::Services/Index', [
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
        $business = Business::whereUuid($businessUuid)->firstOrfail();
        $subscriptionPermission = $this->checkAllowedProducts($business);
        if (!$subscriptionPermission) {
            flash('You can not add more services due to subscription limitations.', 'danger');
            return \redirect()->back();
        }

        $mediaSizes = \config()->get('services-module.media.services');
        $levelTwoTags = null;
        $levelTwoTags = $this->business->standardTags()->whereHas('levelTwo',function($query) use ($moduleId) {
            $query->where('L1', $moduleId);
        })->select(['id', 'name as text', 'slug'])->get();
        
        return Inertia::render('Services::Services/Create', [
            'mediaSizes' => $mediaSizes,
            'levelTwoTags' => $levelTwoTags
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(ServiceRequest $request, $moduleId, $businessUuid)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule('services');

            $request->merge([
                'business_id' => $this->businessId,
            ]);

            Product::create($request->all());
            \flash('Service created successfully.', 'success');
            DB::commit();

            return \redirect()
                ->route('services.dashboard.service-provider.services.index', [$moduleId, $businessUuid]);
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
        return view('services::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
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
            return Inertia::render('Services::Services/Edit', [
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
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(ServiceRequest $request, $moduleId, $busiessId, $uuid)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule('services');
            $product = Product::whereUuid($uuid)->firstOrFail();
            $discount = null;

            $product->update($request->all());
            flash('Service basic information updated successfully.', 'success');
            DB::commit();
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this service.', 'danger');
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
     * @param int $id
     * @return Renderable
     */
    public function destroy($moduleId, $businessUuidd, $uuid, Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $product = Product::where('uuid', $uuid)->firstOrfail();
            $product->delete();
            flash('Service deleted succesfully', 'success');
            if ($currentCount > 0) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('services.dashboard.service-provider.services.index', [$moduleId,$businessUuidd, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this service', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function changeStatus($moduleId, $businessUuidd, $uuid)
    {
        try {
            $product = Product::where('uuid', $uuid)->firstOrfail();
            $previousProductStatus = $product->status;
            if ($previousProductStatus == 'inactive' && !$product->mainImage()->exists()) {
                flash('Primary image not found. Cannot proceed with status activation.', 'danger');
                return redirect()->back();
            }
            if ($previousProductStatus == 'inactive') {
                //checking subscription permissions starts
                $business = Business::whereUuid($businessUuidd)->firstOrfail();
                $subscriptionPermission = $this->checkAllowedProducts($business);
                if (!$subscriptionPermission) {
                    flash('You can not activate this service due to subscription limitations.', 'danger');
                    return \redirect()->back();
                }
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
                $product->refresh(); 
                ProductPriorityManager::updatePriorityBasedOnStatus($product);
                flash('Service status changed succesfully', 'success');
            }
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this service', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function getTags(Request $request, $moduleId, $businessId, $tagId, $level)
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
                    })->select(['id', 'name as text', 'slug'])->first();
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
