<?php

namespace App\Http\Controllers\Admin\Business;

use App\Models\Tag;
use Inertia\Inertia;
use Stripe\StripeClient;
use App\Models\StandardTag;
use App\Models\DeliveryZone;
use App\Models\TagHierarchy;
use Illuminate\Http\Request;
use App\Jobs\AssignTagsToStore;
use App\Models\BusinessSetting;
use Illuminate\Http\JsonResponse;
use App\Jobs\CheckProductTagError;
use App\Traits\StripeSubscription;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Http\Requests\AdminSettingRequest;
use App\Jobs\ActivateDeactivateTagProducts;
use App\Http\Requests\BusinessSettingRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
     *
     * @return \Illuminate\Http\Response
     */
    public function index($businessUuid)
    {
        $business = Business::with(['banner', 'logo', 'thumbnail', 'settings' => function ($query) {
            $query->whereIn('key', ['minimum_purchase', 'tax_apply', 'global_price', 'delivery_time', 'pickup_time', 'tax_type', 'tax_percentage', 'deliverable']);
        }])->where('uuid', $businessUuid)->first();
        return Inertia::render('Business/Settings/Index', [
            'business' => $business,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BusinessSettingRequest $request, $businessId, $businessUuid)
    {
        try {
            DB::beginTransaction();
            $business = Business::where('uuid', $businessUuid)->first();
            //checking subscription and allowing feature business
            if ($request->boolean('isFeatured')) {
                $permission = $this->checkActiveBusinesses($business, 'check_featured_businesses');
                if (!$permission) {
                    DB::rollBack();
                    flash('You have featured maximum no of businesses according to your subscription plan.', 'danger', 'dashboard.subscription.subscribe.index');
                    return redirect()->back();
                }
            }
            $business->update([
                'url' => $request->businessUrl,
                'is_direct_url' => $request->input('businessUrl') ? 1 : 0,
                'is_featured' => $request->input('isFeatured'),
            ]);
            //Getting Delivery time and converting it to time string
            $delivery_time = $request->delivery_time['DD'] .
                ':' . $request->delivery_time['HH'] .
                ':' . $request->delivery_time['MM'];

            // updating business settings
            foreach ($request->settings as $setting) {
                $businessSetting = BusinessSetting::find($setting['id']);
                $businessSetting->value = $setting['key'] == 'delivery_time' ? $delivery_time : $setting['value'];
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function standardTagsAdminSettings($module_id, $businessUuid)
    {
        $business = Business::with(['deliveryZone'])->with('standardTags', function ($query) {
            $query->asTag()->where('type', 'module')->orWhere('type', 'product')->orWhere('type', 'industry')->active();
        })->where('uuid', $businessUuid)->first();

        // getting all level two tags
        $industryTags = StandardTag::asTag()->where('type', 'product')->where(function ($query) use ($module_id) {
            $query->whereHas('tagHierarchies', function ($query) use ($module_id) {
                $query->where('L1', $module_id)
                    ->where('level_type', 2);
            })->orWhereHas('levelTwo', function ($query) use ($module_id) {
                $query->where('L1', $module_id);
            });
        })->orWhere('type', 'industry')->active()->get();

        $businessIndustryTags = array_intersect($industryTags->pluck('id')->toArray(), $business->standardTags()->pluck('id')->toArray());
        // getting all level three tags
        $productTags = StandardTag::asTag()->where('type', 'product')->where(function ($query) use ($module_id, $businessIndustryTags, $business) {
            $query->whereHas('tagHierarchies', function ($query) use ($module_id, $businessIndustryTags, $business) {
                $query->where('L1', $module_id)
                    ->when($business->standardTags()->count() > 0, function ($query) use ($business, $businessIndustryTags) {
                        $query->whereIn('L2', $businessIndustryTags);
                    })
                    ->where('level_type', 3);
            })->orWhereHas('levelThree', function ($query) use ($module_id, $businessIndustryTags, $business) {
                $query->where('L1', $module_id)
                    ->when($business->standardTags()->count() > 0, function ($query) use ($business, $businessIndustryTags) {
                        $query->whereIn('L2', $businessIndustryTags);
                    });
            });
        })->active()->get();

        return Inertia::render('Business/AdminSettings/AssignTags/Index', [
            'business' => $business,
            'industryTagsList' => $industryTags,
            'productTagsList' => $productTags,
        ]);
    }

    public function assignTags($module_id, $businessUuid, $tag)
    {
        try {
            DB::beginTransaction();
            $business = Business::where('uuid', $businessUuid)->firstOrFail();
            $tags = collect(json_decode($tag));
            //Job to assign tags to product
            // AssignTagsToStore::dispatch($business, $tags, $business->standardTags);
            // ActivateDeactivateTagProducts::dispatch($business, $tags, $business->standardTags);
            $standardTags = $business->standardTags()->whereType('product')->whereNotIn('id', $tags->pluck('id'))->pluck('id');
            $business->standardTags()->detach($standardTags);
            CheckProductTagError::dispatch($business);
            $business->standardTags()->syncWithoutDetaching($tags->pluck('id'));
            DB::commit();
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return \redirect()->back()->withErrors([
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return \redirect()->back()->withErrors([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function adminSettings($module_id, $businessUuid)
    {
        $business = Business::with(['standardTags', 'settings', 'deliveryZone'])
            ->where('uuid', $businessUuid)->first();
        return Inertia::render('Business/AdminSettings/Settings/Index', [
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
        return Inertia::render('Business/AdminSettings/AssignFeeType/Index', [
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

    public function filterIndustryTags($module_id, $businessUuid, Request $request)
    {
        try {
            $productTags = StandardTag::asTag()->where('type', 'product')->where(function ($query) use ($module_id, $request) {
                $query->whereHas('tagHierarchies', function ($query) use ($module_id, $request) {
                    $query->where('L1', $module_id)
                        ->when($request->has('filterProductTags'), function ($query) use ($request) {
                            $indutryTagsIds = collect(json_decode($request->input('filterProductTags')))->pluck('id')->toArray();
                            $query->whereIn('L2', $indutryTagsIds);
                        })
                        ->where('level_type', 3);
                })->orWhereHas('levelThree', function ($query) use ($module_id, $request) {
                    $query->where('L1', $module_id)
                        ->when($request->has('filterProductTags'), function ($query) use ($request) {
                            $indutryTagsIds = collect(json_decode($request->input('filterProductTags')))->pluck('id')->toArray();
                            $query->whereIn('L2', $indutryTagsIds);
                        });
                });
            })->active()->get();
            return \response()->json([
                'data' => collect($productTags),
                'status' => 'success',
                'message' => 'Level 3 Tags Updated.'
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return \response()->json([
                'status' => 'error',
                'message' => 'Unable to find Tags'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return \response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
