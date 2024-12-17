<?php

namespace Modules\Notices\Http\Controllers\Dashboard\Notice;

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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;
use Modules\Notices\Http\Requests\NoticeRequest;
use App\Traits\ProductPriorityManager;

class NoticeController extends Controller
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
        $orderBy = request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc';
        $limit = \config()->get('settings.pagination_limit');
        $standardTags = StandardTag::where('type', '!=', 'module')->select(['id', 'name as text', 'slug'])
            ->whereHas('productTags', function ($query) {
                $query->where('business_id', $this->businessId);
            })->get();
        $products = Product::with(['mainImage'])
            ->where(function ($query) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('description', 'like', '%' . $keyword . '%');
                }
                if (request()->form && isset(request()->form['status'])) {
                    $query->where('status', request()->form['status']);
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

        return Inertia::render('Notices::Notices/Index', [
            'productsList' => $products,
            'searchedKeyword' => request()->keyword,
            'orderBy' => request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc',
            'status' => request()->form && isset(request()->form['status']) ? request()->form['status'] : null,
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
        // $subscriptionPermission = $this->checkAllowedProducts($business);
        // if (!$subscriptionPermission) {
        //     flash('You can not add more services due to subscription limitations.', 'danger');
        //     return \redirect()->back();
        // }

        $mediaSizes = \config()->get('notices.media.notice');
        $levelTwoTags = null;
        $levelTwoTags = $this->business->standardTags()->where('slug', 'legal-notices')->whereHas('levelTwo')->select(['id', 'name as text', 'slug'])->get();
        if ($levelTwoTags->count() == 0) {
            $levelTwoTags = StandardTag::whereHas('levelTwo', function ($query) use ($moduleId) {
                $query->where('L1', $moduleId);
            })->select(['id', 'name as text', 'slug'])->get();
        }
        return Inertia::render('Notices::Notices/Create', [
            'mediaSizes' => $mediaSizes,
            'levelTwoTags' => $levelTwoTags
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(NoticeRequest $request, $moduleId, $businessUuid)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule('notices');

            $request->merge([
                'business_id' => $this->businessId,
            ]);

            Product::create($request->all());
            \flash('Notice created successfully.', 'success');
            DB::commit();

            return \redirect()
                ->route('notices.dashboard.organization.notices.index', [$moduleId, $businessUuid]);
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
        return view('notices::show');
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
            $levelTwoTags = $this->business->standardTags()->where('name', 'like', '%' . $product->type . '%')->whereHas('levelTwo')->select(['id', 'name as text', 'slug'])->get();
            if ($levelTwoTags->count() == 0) {
                $levelTwoTags = StandardTag::where('name', 'like', '%' . $product->type . '%')->whereHas('levelTwo', function ($query) use ($moduleId) {
                    $query->where('L1', $moduleId);
                })->select(['id', 'name as text', 'slug'])->get();
            }
            return Inertia::render('Notices::Notices/Edit', [
                'product' => $product,
                'levelTwoTags' => $levelTwoTags,
                'productLevelTwoTag' => $productLevelTwoTag
            ]);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this notice', 'danger');
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
    public function update(NoticeRequest $request, $moduleId, $busiessId, $uuid)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule('notices');
            $product = Product::whereUuid($uuid)->firstOrFail();

            $product->update($request->all());
            flash('Notice basic information updated successfully.', 'success');
            DB::commit();
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this notice.', 'danger');
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
    public function destroy($moduleId, $businessUuidd, $uuid,Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $product = Product::where('uuid', $uuid)->firstOrfail();
            $product->delete();
            flash('Notice deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('notices.dashboard.organization.notices.index', [$moduleId,$businessUuidd, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this notice', 'danger');
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
            if ($previousProductStatus == 'inactive') {
                //checking subscription permissions starts
                $business = Business::whereUuid($businessUuidd)->firstOrfail();
                $subscriptionPermission = $this->checkAllowedProducts($business);
                if (!$subscriptionPermission) {
                    flash('You can not activate this notice due to subscription limitations.', 'danger');
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
                flash('notice status changed succesfully', 'success');
            }
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this notice', 'danger');
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
            if ($level == 1) {
                $nextLevelTags = StandardTag::where('name', 'like', '%' . request()->tagId . '%')
                    ->whereHas('levelTwo', function ($query) use ($moduleId) {
                        $query->where('L1', $moduleId);
                    })->select(['id', 'name as text', 'slug'])->paginate(50);
            } else if ($level == 2) {
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
                    })->whereHas('tagHierarchies', function ($query) use ($moduleId, $tagId) {
                        $query->where('L1', $moduleId)->where('L2', request()->levelTwoTag)->where('L3', $tagId);
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
