<?php

namespace Modules\Automotive\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Business;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use App\Jobs\AssignTagsToStore;
use Illuminate\Http\JsonResponse;
use App\Jobs\CheckProductTagError;
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

        return Inertia::render('Automotive::Garage/AdminSettings/AssignTags/Index', [
            'business' => $business,
            'industryTagsList' => $industryTags,
            'productTagsList' => $productTags,
        ]);
    }

    public function chatAdminSettings($module_id, $businessUuid)
    {
        $business = Business::with(['deliveryZone'])->with('standardTags', function ($query) {
            $query->asTag()->where('type', 'module')->orWhere('type', 'product')->orWhere('type', 'industry')->active();
        })->where('uuid', $businessUuid)->first();

        return Inertia::render('Automotive::Garage/AdminSettings/ChatSetting/Index', [
            'business' => $business,
        ]);
    }

    public function assignTags($module_id, $businessUuid, Request $request)
    {
        try {
            DB::beginTransaction();
            $business = Business::where('uuid', $businessUuid)->firstOrFail();
            $tag = $request->input('tag');
            $tags = collect(json_decode($tag));
            $standardTags = $business->standardTags()->whereType('product')->whereNotIn('id', $tags->pluck('id'))->pluck('id');
            $business->standardTags()->detach($standardTags);
            CheckProductTagError::dispatch($business);
            $business->standardTags()->syncWithoutDetaching($tags->pluck('id'));
            DB::commit();
            flash('Tags are assigned succesfully', 'success');
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
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteTags($module_id, $businessUuid, $tag_id, Request $request){
        try{
            $business = Business::where('uuid', $businessUuid)->firstOrFail();
            $tagUsedInProducts = $business->products()
            ->whereHas('standardTags', function ($query) use ($tag_id) {
                $query->where('standard_tag_id', $tag_id);
            })
            ->exists();
            if ($tagUsedInProducts) {
                return response()->json([
                    'message' => 'Tag in use, unable to delete.'
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
            return $tagUsedInProducts;
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function removeProductTags($module_id, $businessUuid, Request $request)
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
}
