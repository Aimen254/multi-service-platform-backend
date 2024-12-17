<?php

namespace Modules\Government\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SettingsController extends Controller
{
    public function standardTagsAdminSettings($module_id, $businessUuid)
    {
        $business = Business::where('uuid', $businessUuid)->first();
        // getting all level two tags
        $industryTags = StandardTag::asTag()->where('type', 'product')->where(function ($query) use ($module_id) {
            $query->whereHas('tagHierarchies', function ($query) use ($module_id) {
                $query->where('L1', $module_id)
                    ->where('level_type', 2);
            })->orWhereHas('levelTwo', function ($query) use ($module_id) {
                $query->where('L1', $module_id);
            });
        })->active()->get();

        $businessIndustryTags = $business->standardTags()->asTag()->whereHas('levelTwo', function ($query) use ($module_id) {
            $query->where('L1', $module_id);
        })->get();
        $businessLevelThreeTags = $business->standardTags()->where(function ($query) use($module_id, $businessIndustryTags) {
            $query->whereHas('levelThree', function ($query) use ($businessIndustryTags, $module_id) {
                $query->where('L1', $module_id)->whereIn('L2', $businessIndustryTags->pluck('id')->toArray());
            })->orWhereHas('tagHierarchies', function ($query) use ($businessIndustryTags, $module_id) {
                $query->where('L1', $module_id)->whereIn('L2', $businessIndustryTags->pluck('id')->toArray());
                $query->where('level_type', 3);
            });
        })->asTag()->get();

        return Inertia::render('Government::Department/AdminSettings/AssignTags/Index', [
            'business' => $business,
            'industryTagsList' => $industryTags,
            'businessIndustryTags' => $businessIndustryTags,
            'levelThreeTags' => $businessLevelThreeTags
        ]);
    }
    public function chatAdminSettings($module_id, $businessUuid)
    {
        $business = Business::with(['deliveryZone'])->with('standardTags', function ($query) {
            $query->asTag()->where('type', 'module')->orWhere('type', 'product')->orWhere('type', 'industry')->active();
        })->where('uuid', $businessUuid)->first();

        return Inertia::render('Employment::Employers/AdminSettings/ChatSetting/Index', [
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
            // CheckProductTagError::dispatch($business);
            $business->standardTags()->syncWithoutDetaching($tags->pluck('id'));
            // dd($business->standardTags()->get());
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
}
