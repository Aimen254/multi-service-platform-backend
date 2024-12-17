<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserStandardTagsSettingController extends Controller
{
    public function userStandardTags($module_id, $userId)
    {
        $user = User::where('id', $userId)->first();

        // getting all level two tags
        $industryTags = StandardTag::asTag()->where('type', 'product')->where(function ($query) use ($module_id) {
            $query->whereHas('tagHierarchies', function ($query) use ($module_id) {
                $query->where('L1', $module_id)
                    ->where('level_type', 2);
            })->orWhereHas('levelTwo', function ($query) use ($module_id) {
                $query->where('L1', $module_id);
            });
        })->active()->get();

        $userIndustryTags = $user?->standardTags()->asTag()->whereHas('levelTwo', function ($query) use ($module_id) {
            $query->where('L1', $module_id);
        })->get();

        $userLevelThreeTags = $user?->standardTags()->whereHas('levelThree', function ($query) use ($module_id) {
            $query->where('L1', $module_id);
        })->orWhereHas('tagHierarchies', function ($query) use ($userIndustryTags, $module_id) {
            $query->whereIn('L2', $userIndustryTags->pluck('id')->toArray());
            $query->where('level_type', 3)->where('L1', $module_id);
        })->asTag()->get();


        $data = [
            'levelTwoTags' => $industryTags,
            'assignedLevelTwoTags' => $userIndustryTags,
            'assignedLevelThreeTags' => $userLevelThreeTags
        ];

        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'data' => $data,
        ], JsonResponse::HTTP_OK);
    }

    public function assignTags(Request $request, $module_id, $userId)
    {
        try {
            DB::beginTransaction();
            $user = User::where('id', $userId)->firstOrFail();
            $tags = collect(json_decode($request->input('tags')));
            // attaching module tag
            $moduleTag = StandardTag::where('id', $module_id)->orWhere('slug', $module_id)->first();
            $moduleTag = (object) ['id' => $moduleTag->id, 'text' => $moduleTag->name, 'type' => $moduleTag->type, 'status' => $moduleTag->status, 'slug' => $moduleTag->slug, 'priority' => $moduleTag->priority];
            $tags = $tags->merge([$moduleTag]);

            //Job to assign tags to product
            // AssignTagsToStore::dispatch($user, $tags, $user->standardTags);
            // ActivateDeactivateTagProducts::dispatch($user, $tags, $user->standardTags);
            $standardTags = $user->standardTags()->whereType('product')->whereNotIn('id', $tags->pluck('id'))->pluck('id');
            $user->standardTags()->detach($standardTags);
            // CheckProductTagError::dispatch($user);
            $user->standardTags()->syncWithoutDetaching($tags->pluck('id'));

            DB::commit();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Tags assigned successfully.',
            ], JsonResponse::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getLevelThreeTags($moduleId)
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

    public function removeProductTags(Request $request, $module_id)
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
