<?php

namespace Modules\Retail\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Business;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use App\Jobs\AssignTagsToStore;
use Illuminate\Http\JsonResponse;
use App\Jobs\CheckProductTagError;
use App\Models\Product;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Retail\Entities\DeliveryZone;
use Illuminate\Contracts\Support\Renderable;
use Modules\Retail\Entities\BusinessSetting;
use Modules\Retail\Http\Requests\AdminSettingRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Retail\Http\Requests\BusinessSettingRequest;

class SettingsController extends Controller
{
    use StripeSubscription;
    public StripeClient $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $businessUuid)
    {
        $business = Business::with(['banner', 'logo', 'thumbnail', 'settings' => function ($query) {
            $query->whereIn('key', ['minimum_purchase', 'tax_apply', 'tax_type', 'tax_percentage', 'deliverable']);
        }])->where('uuid', $businessUuid)->first();
        return Inertia::render('Retail::Business/Settings/Index', [
            'business' => $business,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('retail::create');
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
        return view('retail::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('retail::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(BusinessSettingRequest $request, $moduleId, $businessId, $businessUuid)
    {
        try {
            DB::beginTransaction();
            $business = Business::where('uuid', $businessUuid)->first();
            // checking subscription and allowing feature business
            // if ($request->boolean('isFeatured')) {
            //     $permission = $this->checkActiveBusinesses($business, 'check_featured_businesses', $moduleId);
            //     if (!$permission) {
            //         DB::rollBack();
            //         flash('You have featured maximum no of businesses according to your subscription plan.', 'danger', 'dashboard.subscription.subscribe.index');
            //         return redirect()->back();
            //     }
            // }
            $business->update([
                'url' => $request->businessUrl,
                'is_direct_url' => $request->input('businessUrl') ? 1 : 0,
                'is_featured' => $request->input('isFeatured'),
            ]);
            // updating business settings
            foreach ($request->settings as $setting) {
                $businessSetting = BusinessSetting::find($setting['id']);
                $businessSetting->value = $setting['key'] ==  $setting['value'];
                $businessSetting->save();
            }
            $businessDeliverAble = BusinessSetting::where('key', 'deliverable')->where('business_id', $business->id)->first();
            if ($businessDeliverAble) {
                $businessDeliverAble->value = $request->deliverable ? 1 : 0;
                $businessDeliverAble->save();
            }
            DB::commit();
            flash('Settings updated Sucessfully!', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this business setting', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
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

    public function standardTagsAdminSettings($module_id, $businessUuid)
    {
        $business = Business::with(['deliveryZone'])
            ->where('uuid', $businessUuid)->first();
        // getting all level two tags
        $industryTags = StandardTag::asTag()->where('type', 'product')->where(function ($query) use ($module_id) {
            $query->whereHas('tagHierarchies', function ($query) use ($module_id) {
                $query->where('L1', $module_id)
                    ->where('level_type', 2);
            })->orWhereHas('levelTwo', function ($query) use ($module_id) {
                $query->where('L1', $module_id);
            });
        })->active()->get();

        $businessIndustryTags = $business->standardTags()->asTag()->whereHas('levelTwo')->get();
        $businessLevelThreeTags = $business->standardTags()->where(function ($query) use ($module_id, $businessIndustryTags) {
            $query->whereHas('levelThree', function ($query) use ($businessIndustryTags, $module_id) {
                $query->where('L1', $module_id)->whereIn('L2', $businessIndustryTags->pluck('id')->toArray());
            })->orWhereHas('tagHierarchies', function ($query) use ($businessIndustryTags, $module_id) {
                $query->where('L1', $module_id)->whereIn('L2', $businessIndustryTags->pluck('id')->toArray());
                $query->where('level_type', 3);
            });
        })->asTag()->get();

        return Inertia::render('Retail::Business/AdminSettings/AssignTags/Index', [
            'business' => $business,
            'industryTagsList' => $industryTags,
            'businessIndustryTags' => $businessIndustryTags,
            'levelThreeTags' => $businessLevelThreeTags
        ]);
    }

    public function assignTags($module_id, $businessUuid, Request $request)
    {
        try {
            DB::beginTransaction();
            $business = Business::where('uuid', $businessUuid)->firstOrFail();
            $tag = $request->input('tag');
            $tags = collect(json_decode($tag));
            //Job to assign tags to product
            AssignTagsToStore::dispatch($business, $tags, $business->standardTags);
            // ActivateDeactivateTagProducts::dispatch($business, $tags, $business->standardTags);
            $standardTags = $business->standardTags()->whereType('product')->whereNotIn('id', $tags->pluck('id'))->pluck('id');
            $business->standardTags()->detach($standardTags);
            CheckProductTagError::dispatch($business);
            $business->standardTags()->syncWithoutDetaching($tags->pluck('id'));
            // dd($business->standardTags()->get());
            DB::commit();
            flash('Tags are assigned succesfully', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this property', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    public function adminSettings($module_id, $businessUuid)
    {
        $business = Business::with(['standardTags', 'settings', 'deliveryZone'])
            ->where('uuid', $businessUuid)->first();
        return Inertia::render('Retail::Business/AdminSettings/Settings/Index', [
            'business' => $business,
        ]);
    }

    public function updateSettings(AdminSettingRequest $request)
    {
        try {
            foreach ($request->settings as $setting) {
                $businessSetting = BusinessSetting::find($setting['id']);
                $businessSetting->value = $setting['value'];
                $businessSetting->save();
            }
            flash('Settings updated Sucessfully!', 'success');
            return \redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    public function assignFeeType($category, $businessUuid)
    {
        $business = Business::with(['standardTags', 'settings', 'deliveryZone'])
            ->where('uuid', $businessUuid)->first();
        return Inertia::render('Retail::Business/AdminSettings/AssignFeeType/Index', [
            'business' => $business,
        ]);
    }

    public function updateFeeType(Request $request)
    {
        try {
            DB::beginTransaction();
            $delivery_zone = DeliveryZone::findOrFail($request->delivery_zone_id);
            $delivery_zone->update([
                'fee_type' => $request->fee_type,
            ]);
            DB::commit();
            flash('Delivey Zone updated Sucessfully!', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this delivery zone', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    public function removeProductTags($module_id, $businessUuid, Request $request)
    {
        try {
            // Decode and validate the filterProductTags input
            $filterProductTags = json_decode($request->input('filterProductTags'), true);
            if (!isset($filterProductTags['tag'])) {
                throw new \InvalidArgumentException('Invalid filterProductTags format.');
            }

            $firstTag = $filterProductTags['tag'];
                if (isset($firstTag['pivot'])) {
                $businessId = $firstTag['pivot']['business_id'];

                $hasTag = Product::where('business_id', $businessId)
                    ->whereHas('standardTags', function ($query) use ($firstTag) {
                        $query->where('id', $firstTag['id']);
                    })
                    ->exists();

                // If the tag is attached to a product, prevent detaching
                if ($hasTag) {
                    return response()->json([
                        'message' => 'Cannot remove the tag as it is linked against business Product'
                    ], JsonResponse::HTTP_CONFLICT);
                }
            }

            // Proceed with removing the tag if not attached
            $productTags = StandardTag::asTag()
                ->where('type', 'product')
                ->where(function ($query) use ($module_id, $request) {
                    $query->whereHas('tagHierarchies', function ($query) use ($module_id, $request) {
                        $query->where('L1', $module_id)
                            ->when($request->has('filterProductTags'), function ($query) use ($request) {
                                $industryTagsIds = collect(json_decode($request->input('filterProductTags')))->pluck('id')->toArray();
                                $query->whereIn('L2', $industryTagsIds);
                            })
                            ->where('level_type', 3);
                    })->orWhereHas('levelThree', function ($query) use ($module_id, $request) {
                        $query->where('L1', $module_id)
                            ->when($request->has('filterProductTags'), function ($query) use ($request) {
                                $industryTagsIds = collect(json_decode($request->input('filterProductTags')))->pluck('id')->toArray();
                                $query->whereIn('L2', $industryTagsIds);
                            });
                    });
                })
                ->active()
                ->get();

            // Return the response with the updated tags
            return response()->json([
                'data' => collect($productTags),
                'status' => 'success',
                'message' => 'Level 3 Tags Updated.'
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to find Tags'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    public function getLevelThreeTags($moduleId, $businessUuid)
    {
        try {
            $levelTwoIds = collect(json_decode(request()->input('levelTwo')))->pluck('id')->toArray();
            $productTags = StandardTag::asTag()->where('type', 'product')->where(function ($query) use ($moduleId, $levelTwoIds) {
                $query->whereHas('tagHierarchies', function ($subQuery) use ($moduleId, $levelTwoIds) {
                    $subQuery->where('L1', $moduleId)->whereIn('L2', $levelTwoIds)
                        ->where('level_type', 3);
                })->orWhereHas('levelThree', function ($query) use ($moduleId, $levelTwoIds) {
                    $query->where('L1', $moduleId);
                    $query->whereIn('L2', $levelTwoIds);
                });
            })->active()->get();
            return \response()->json([
                'data' => collect($productTags),
                'status' => 'success',
                'message' => 'Level 3 Tags Updated.'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return \response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function chatAdminSettings($module_id, $businessUuid)
    {
        $business = Business::with(['deliveryZone'])->with('standardTags', function ($query) {
            $query->asTag()->where('type', 'module')->orWhere('type', 'product')->orWhere('type', 'industry')->active();
        })->where('uuid', $businessUuid)->first();

        return Inertia::render('Retail::Business/AdminSettings/ChatSetting/Index', [
            'business' => $business,
        ]);
    }

    public function enableDisableChat($module_id, $businessUuid, Request $request)
    {
        try {
            DB::beginTransaction();
            $business = Business::where('uuid', $businessUuid)->firstOrFail();
            $business->can_chat = $request->can_chat;
            $business->save();
            DB::commit();
            flash('Chat setting updated successfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this property', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
