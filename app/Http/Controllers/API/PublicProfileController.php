<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\StandardTag;
use App\Models\PublicProfile;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\PublicProfileRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Transformers\PublicProfileTransformer;

class PublicProfileController extends Controller
{
    /**
     * Display a listing of the specific resource.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index($moduleId)
    {
        $keyword = request()->input('keyword');
        try {
            $paginate = null;
            $limit = request()->input('limit') ? request()->input('limit') : \config()->get('settings.pagination_limit');
            $module = StandardTag::where('id', $moduleId)->orWhere('slug', $moduleId)->first();
            $publicProfile = PublicProfile::where('user_id', auth('sanctum')->user()->id)
                ->where('module_id', $module?->id);
            if($module?->slug == 'posts') {
               $publicProfile = $publicProfile->when(request()->input('keyword'), function($query) {
                    $query->whereRaw('name LIKE ?', ['%' . request()->input('keyword') . '%']);
               })->paginate($limit);
               $paginate = apiPagination($publicProfile, $limit);
               $publicProfile = (new PublicProfileTransformer)->transformCollection($publicProfile);
            } else {
                $publicProfile = $publicProfile->first();
                $publicProfile = (new PublicProfileTransformer)->transform($publicProfile);
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Profile fetched successfull!',
                'data' => $publicProfile,
                'meta' => $paginate,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $exception->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    private function transform($publicProfile): array
    {
        return [
            'user_id' => $publicProfile?->user_id,
            'module_id' => $publicProfile?->module_id,
            'name' => $publicProfile?->name,
            'image' => getImage($publicProfile?->image, 'avatar'),
            'about' => $publicProfile?->description,
            'cover_image' => getImage($publicProfile?->cover_image, 'banners'),
            'is_name_visible' => $publicProfile?->is_name_visible
        ];
    }

    /**
     * Update or store a resource in storage.
     *
     * @param  \Illuminate\Http\PublicProfileRequest  $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateOrStore(PublicProfileRequest $request)
    {
        try {
            DB::beginTransaction();
            
            if($request->input('module') == 'posts') {
                $publicProfile = $this->postModulePublicProfile($request->all());
            } else {
                $publicProfile = PublicProfile::updateOrCreate(
                    [
                        'module_id' => $request->module_id,
                        'user_id' => auth('sanctum')->user()->id
                    ],
                    $request->validated()
                );
    
                if ($request->input('level_two_tags') && $request->input('level_three_tags')) {
                    $user = User::where('id', auth('sanctum')->user()->id)->firstOrFail();
                    $tags = collect($request->input('level_two_tags'));
                    $moduleTag = StandardTag::where('id', $request->module_id)->orWhere('slug', $request->module_id)->first();
                    $moduleTag = (object) ['id' => $moduleTag->id, 'text' => $moduleTag->name, 'type' => $moduleTag->type, 'status' => $moduleTag->status, 'slug' => $moduleTag->slug, 'priority' => $moduleTag->priority];
                    $tags = $tags->merge([$moduleTag]);
                    $tags = $tags->merge(collect($request->input('level_three_tags')));
                    $standardTags = $user->standardTags()->whereType('product')->whereNotIn('id', $tags->pluck('id'))->pluck('id');
                    $user->standardTags()->detach($standardTags);
                    $user->standardTags()->syncWithoutDetaching($tags->pluck('id'));
                }
            }
            DB::commit();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Profile saved successfully!',
                'data' => (new PublicProfileTransformer)->transform($publicProfile)
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update or store a image resource in storage.
     *
     * @param  \Illuminate\Http\PublicProfileRequest  $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateOrStoreImage(PublicProfileRequest $request)
    {
        try {
            $data = [
                'user_id' => auth('sanctum')->user()->id,
                'module_id' => $request->module_id
            ];

            if ($request?->image) {
                $imageConfig = config()->get('image.media.public_profile.avatar');
                $avatar = saveResizeImage($request->image, 'public-profile/avatars', $imageConfig['width'], $imageConfig['height'], $request->image->extension());
                $data['image'] = $avatar;
                $message = 'Avatar saved successfully.';
            } elseif ($request?->cover_image) {
                $imageConfig = config()->get('image.media.public_profile.banner');
                $coverImage = saveResizeImage($request->cover_image, 'public-profile/banners', $imageConfig['width'], $imageConfig['height'], $request->cover_image->extension());
                $data['cover_image'] = $coverImage;
                $message = 'Cover image saved successfully.';
            }

            $media = PublicProfile::updateOrCreate(
                [
                    'module_id' => $request->module_id,
                    'user_id' => auth('sanctum')->user()->id
                ],
                $data
            );

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $message,
                'data' => $media,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id) {
        try{
            $profile = PublicProfile::find($id);
            $publicProfile = (new PublicProfileTransformer)->transform($profile);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $publicProfile,
            ], JsonResponse::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function postModulePublicProfile($data) {
        $data['user_id'] = auth('sanctum')->user()->id;
        $profile = PublicProfile::find($data['id']);

        if(isset($data['image']) && $data['image']) {
            deleteFile($profile?->image);
            $imageConfig = config()->get('image.media.public_profile.avatar');
            $avatar = saveResizeImage($data['image'], 'public-profile/avatars', $imageConfig['width'], $imageConfig['height'], $data['image']->extension());
            $data['image'] = $avatar;
        }

        if(isset($data['cover_image']) && $data['cover_image']) {
            deleteFile($profile?->cover_image);
            $imageConfig = config()->get('image.media.public_profile.banner');
            $coverImage = saveResizeImage($data['cover_image'], 'public-profile/banners', $imageConfig['width'], $imageConfig['height'], $data['cover_image']->extension());
            $data['cover_image'] = $coverImage;
        }
        if($profile) {
            $profile->update($data);
            $profile = $profile->refresh();
        } else {
            $profile = PublicProfile::create($data);
        }
        return $profile;
    }

    public function destroy($id) {
        try {
            $profile = PublicProfile::find($id);
            if($profile) {
                deleteFile($profile?->image);
                deleteFile($profile?->cover_image);
                $profile->delete();
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => "Public Profile Deleted Successfully.",
            ], JsonResponse::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
