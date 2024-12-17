<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\FollowProfileRequest;
use App\Models\PublicProfile;
use App\Models\PublicProfileFollower;
use App\Transformers\PublicProfileTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowProfileController extends Controller
{

    // follow lists, requests, followers
    public function index($type) {
       $data = [];
       $limit = request()->input('limit') ? request()->input('limit') : \config()->get('settings.pagination_limit');
       $profile = PublicProfile::where('id', request()->input('profile_id'))->first();

       if ($type == 'follow-request') {
            $data = $profile->followers()->where(function($query) {
                $query->whereRaw('name LIKE ?', ['%' . request()->input('keyword') . '%']);
            })
            ->wherePivot('status', 'pending');
       } else if ($type == 'followers') {
            $data = $profile->followers()->with(['followers' => function($query) {
                $query->where('public_profiles.id', request()->input('profile_id'));
            }])->where(function($query) {
                $query->whereRaw('name LIKE ?', ['%' . request()->input('keyword') . '%']);
            })
            ->wherePivot('status', 'accepted');
       } else {
            $data = $profile->following()->where(function($query) {
                $query->whereRaw('name LIKE ?', ['%' . request()->input('keyword') . '%']);
            })
            ->wherePivot('status', 'accepted');
       }
       $data = $data->paginate($limit);
       $paginate = apiPagination($data, $limit);
       return response()->json([
        'status' => JsonResponse::HTTP_OK,
        'data' => (new PublicProfileTransformer)->transformCollection($data),
        'meta' => $paginate
    ], JsonResponse::HTTP_OK);
    }

    public function followProfile(FollowProfileRequest $request) {
        try {
            $followedProfile = PublicProfile::where('id', $request->input('following_public_profile_id'))->first();
            $message = 'You are now following this profile';
            $follow = true;
            if ($followedProfile->is_public) {
                if($followedProfile->followers()->where('public_profiles.id', $request->input('follower_public_profile_id'))->exists()) {
                    $followedProfile->followers()->detach($request->input('follower_public_profile_id'));
                    $message = 'You have unfollowed this profile';
                    $follow = false;
                } else {
                    $followedProfile->followers()->attach($request->input('follower_public_profile_id'), ['status' => 'accepted']);
                    $follow = true;
                }
            } else {
                $existingFollow = $followedProfile->followers()->where('public_profiles.id', $request->input('follower_public_profile_id'))->first();
                
                if ( $existingFollow ) {
                    if ($existingFollow?->pivot?->status == 'accepted') {
                        $followedProfile->followers()->detach($request->input('follower_public_profile_id'));
                        $message = 'You have unfollowed this profile';
                        $follow = false;
                    } else if ($existingFollow?->pivot?->status == 'pending') {
                        $message = 'Follow request already sent.';
                        $follow = false;
                    } else {
                        $followedProfile->followers()->updateExistingPivot($request->input('follower_public_profile_id'), ['status' => 'pending']);
                        $follow = false;
                        $message = 'Follow request sent to this profile';
                    }
                } else {
                    $followedProfile->followers()->attach($request->input('follower_public_profile_id'), ['status' => 'pending']);
                    $follow = false;
                    $message = 'Follow request sent to this profile';
                }
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'follower' => $followedProfile->followers()->where('public_profiles.id', $request->input('follower_public_profile_id'))->first(),
                'message' => $message
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeRequestStatus($id, $type) {
        try {
            $profile = PublicProfile::find($id);
            $status = $type == 'accept' ? 'accepted' : 'rejected';
            $profile->following()->updateExistingPivot(request()->input('profile_id'), [
                'status' => $status
            ]);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'You have ' . $status . ' the request.'
            ], JsonResponse::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function cancelRequest($follower, $id) {
        try {
            $profile = PublicProfile::find($id);
            $profile->followers()->detach($follower);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'You have canceled the request.'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
