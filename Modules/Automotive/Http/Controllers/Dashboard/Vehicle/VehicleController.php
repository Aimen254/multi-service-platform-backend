<?php

namespace Modules\Automotive\Http\Controllers\Dashboard\Vehicle;

use Exception;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Business;
use Stripe\StripeClient;
use App\Models\StandardTag;
use App\Models\TagHierarchy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\ModuleSessionManager;
use Illuminate\Support\Facades\Route;
use App\Traits\ProductPriorityManager;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\Support\Renderable;
use Modules\Automotive\Http\Requests\VehicleRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VehicleController extends Controller
{
    use StripeSubscription;
    public StripeClient $stripeClient;
    protected $grageId;
    protected $grage;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
        $this->grage = Business::where('uuid', Route::current()->parameters['garageId'])->first();
        $this->grageId = $this->grage->id;
        $this->middleware('can:edit_products')->only('edit');
        $this->middleware('can:add_products')->only('create');
        $this->middleware('can:delete_products')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $grageId)
    {
        $maxPrice = Product::where('business_id', $this->grageId)->max('price')
            ? Product::where('business_id', $this->grageId)->max('price') : 1000;
        $minPrice = Product::where('business_id', $this->grageId)->min('price')
            ? Product::where('business_id', $this->grageId)->min('price') : 0;
        $barMinValue = request()->form && isset(request()->form['barMinValue']) ? request()->form['barMinValue'] : null;
        $barMaxValue = request()->form && isset(request()->form['barMaxValue']) ? request()->form['barMaxValue'] : null;
        $orderBy = request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc';
        $limit = \config()->get('settings.pagination_limit');

        $type = request()->form && request()->form['type'] ? request()->form['type'] : null;
        $year = request()->year ? request()->year : null;
        $vehicles = Product::with(['vehicle', 'mainImage'])
            ->where(function ($query) use ($barMinValue, $barMaxValue, $type, $year) {
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
                if ($type) {
                    $query->whereHas('vehicle', function ($subQuery) use ($type) {
                        $subQuery->where('type', $type);
                    });
                }
                if ($year) {
                    $query->whereHas('vehicle', function ($subQuery) use ($year) {
                        $subQuery->where('year', $year);
                    });
                }
            })->where('business_id', $this->grageId)
            ->orderBy('id', $orderBy)
            ->paginate($limit);
        return Inertia::render(
            'Automotive::Vehicles/Index',
            [
                'vehiclesList' => $vehicles,
                'searchedKeyword' => request()->keyword,
                'orderBy' => request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc',
                'status' => request()->form && isset(request()->form['status']) ? request()->form['status'] : null,
                'maxPrice' => $maxPrice,
                'minPrice' => $minPrice,
                'barValueMin' => $barMinValue,
                'barValueMax' => $barMaxValue,
                // 'standardTags' => $standardTags,
                'tag' => request()->form && isset(request()->form['tag']) ? request()->form['tag'] : null,
                'type' => $type,
                'year' => $year
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($moduleId, $grageId)
    {
        // $subscriptionPermission = $this->checkAllowedProducts($this->grage);
        // if (!$subscriptionPermission) {
        //     flash('You can not add more products due to subscription limitations.', 'danger');
        //     return \redirect()->back();
        // }
        // checking subscription permissions ends
        $mediaSizes = \config()->get('automotive.media.vehicle');

        $levelTwoTags = $this->grage->standardTags()->whereHas('levelTwo', function ($query) use ($moduleId) {
            $query->where('L1', $moduleId);
        })->select(['id', 'name as text', 'slug'])->get();
        
        return Inertia::render('Automotive::Vehicles/Create', [
            'mediaSizes' => $mediaSizes,
            'levelTwoTags' => $levelTwoTags,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(VehicleRequest $request, $moduleId, $grageId)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule('automotive');
            $levelThreeTag = StandardTag::findOrFail($request->hierarchies[0]['level_three_tag']);
            $levelFourTag = StandardTag::findOrFail($request->hierarchies[0]['level_four_tags']);
            $request->merge([
                'business_id' => $this->grageId,
                'maker_id' => $levelThreeTag->id,
                'model_id' => $levelFourTag->id
            ]);
            $product = Product::create($request->all());
            $product->vehicle()->create($request->all());
            \flash('Vehicle created successfully.', 'success');
            DB::commit();
            return \redirect()
                ->route('automotive.dashboard.dealership.vehicles.index', [$moduleId, $grageId]);
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
        return view('automotive::show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($moduleId, $grageId, $uuid)
    {
        try {
            $product = Product::with('vehicle')->whereUuid($uuid)->firstOrFail();

            $productTags = $product->standardTags()->withPivot(['hierarchy_id'])->get();
            $hierarchyIds = $productTags->pluck('pivot.hierarchy_id')->filter()->unique();

            $hierarchies = [];

            foreach ($hierarchyIds as $id) {
                $hierarchy = TagHierarchy::where('id', $id)->with(['levelTwo', 'levelThree' => function($query) {
                    $query->select(['id', 'name as text', 'slug']);
                }])->first();

                // Get a single standardTag that meets the criteria
                $levelFour = StandardTag::whereHas('tagHierarchies', function($query) use($moduleId, $hierarchy) {
                    $query->where('L1', $moduleId)->where('L2', $hierarchy->L2)->where('L3', $hierarchy->L3);
                })->whereHas('productTags', function ($subQuery) use ($id, $product) {
                    $subQuery->where('id', $product->id);
                    $subQuery->where('product_standard_tag.hierarchy_id', $id);
                })
                ->select(['id', 'name as text', 'slug'])
                ->first();

                $hierarchies [] = (object) [
                    'L2' => $hierarchy->levelTwo?->id,
                    'L3' => $hierarchy->levelThree,
                    'L4' => $levelFour
                ];
            }

            $levelTwoTags = $this->grage->standardTags()->whereHas('levelTwo', function ($query) use ($moduleId) {
                $query->where('L1', $moduleId);
            })->select(['id', 'name as text', 'slug'])->get();
                        
            return Inertia::render('Automotive::Vehicles/Edit', [
                'product' => $product,
                'levelTwoTags' => $levelTwoTags,
                'productHierarchies' => $hierarchies
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
    public function update(VehicleRequest $request, $moduleId, $grageId, $uuid)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule('automotive');
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
            $levelThreeTag = StandardTag::findOrFail($request->hierarchies[0]['level_three_tag']);
            $levelFourTag = StandardTag::findOrFail($request->hierarchies[0]['level_four_tags']);
            $request->merge([
                'discount_price' => numberFormat($discount),
                'maker_id' => $levelThreeTag->id,
                'model_id' => $levelFourTag->id
            ]);
            $product->update($request->all());
            $product->vehicle()->update([
                'type' => $request->type,
                'trim' => $request->trim,
                'year' => $request->year,
                'mpg' => $request->mpg,
                'stock_no' => $request->stock_no,
                'vin' => $request->vin,
                'sellers_notes' => $request->sellers_notes,
                'mileage' => $request->mileage,
                'maker_id' => $request->maker_id,
                'model_id' => $request->model_id
            ]);
            flash('Vehicle basic information updated successfully.', 'success');
            DB::commit();
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this vehilcle.', 'danger');
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
    public function destroy($moduleId, $grageId, $uuid, Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $product = Product::where('uuid', $uuid)->firstOrfail();
            $product->delete();
            flash('Vehicle deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('automotive.dashboard.dealership.vehicles.index', [$moduleId,$grageId, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this vehicle', 'danger');
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
    public function getTags(Request $request, $moduleId, $grageId, $tagId, $level)
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
                    $nextLevelTags = $this->grage->standardTags()->where(function ($query) use ($tagId, $moduleId) {
                        $query->where('name', 'like', '%' . request()->keyword . '%');
                        $query->whereHas('tagHierarchies', function ($query) use ($tagId, $moduleId) {
                            $query->where('L2', $tagId)->where('L1', $moduleId);
                        })->orwhereHas('levelThree', function ($query) use ($tagId, $moduleId) {
                            $query->where('L2', $tagId)->where('L1', $moduleId);
                        });
                    })->select(['id', 'name as text', 'slug'])->paginate(50);
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
                    })->select(['id', 'name as text', 'slug'])->paginate(50);
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

    /**
     * change status of specified resource from storage.
     *
     * @param  int $uuid, $id
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $request, $moduleId, $grageId, $uuid)
    {
        try {
            $product = Product::where('uuid', $uuid)->firstOrfail();
            $previousProductStatus = $product->status;
            if ($request->status == 'active' && !$product->mainImage()->exists()) {
                flash('Primary image not found. Cannot proceed with status activation.', 'danger');
                return redirect()->back();
            }
            // if (($previousProductStatus == 'inactive' || $previousProductStatus == 'tags_error') &&  ($request->status == 'active' || $request->status == 'sold' || $request->status == 'pending')) {
            //     //checking subscription permissions starts
            //     $business = Business::whereUuid($grageId)->firstOrfail();
            //     $subscriptionPermission = $this->checkAllowedProducts($business);
            //     if (!$subscriptionPermission) {
            //         flash('You can not change status to ' .  $request->status  . ' this vehicle due to subscription limitations.', 'danger');
            //         return \redirect()->back();
            //     }
            //     //checking subscription permissions ends
            // }
            $check = ProductTagsLevelManager::checkProductTagsLevel($product);
            if (!$check) {
                flash('Tag error not resolved', 'danger');
            } else {
                $product->previous_status = $previousProductStatus;
                $product->status = $request->status;
                $product->saveQuietly();
                $product->refresh(); 
                ProductPriorityManager::updatePriorityBasedOnStatus($product);
                flash('Vehicle status changed succesfully', 'success');
            }
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this vehicle', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function searchVehicleTags()
    {
        try {
            $standardTags = StandardTag::where('type', '!=', 'module')->where('name', 'like', '%' . request()->keyword . '%')->select(['id', 'name as text', 'slug'])
                ->whereHas('productTags', function ($query) {
                    $query->where('business_id', $this->grageId);
                })->get();

            return response()->json(
                [
                    'status' => JsonResponse::HTTP_OK,
                    'tags' => $standardTags,
                ],
                JsonResponse::HTTP_OK
            );
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
