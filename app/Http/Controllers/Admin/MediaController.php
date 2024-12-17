<?php

namespace App\Http\Controllers\Admin;

use App\Models\News;
// use App\Models\Business;;
use App\Models\User;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Rules\CheckProductMediaCount;
use App\Models\Business;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Profile\MediaRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MediaController extends Controller
{
    public function deleteMedia(Request $request, $id, $type, $businessId = null, $response = null)
    {
        try {
            if ($businessId) {
                $business = Business::findOrFail($businessId);
                $media = $business->media()->findOrFail($id);
                if ($media) {
                    \deleteFile($media->path);
                }
                $media->delete();
            } else {
                switch ($type) {
                    case 'avatar':
                        $media = User::findOrFail($id);
                        if ($media->avatar) {
                            deleteFile($media->avatar);
                            $media->update([
                                'avatar' => null
                            ]);
                        }
                        break;
                    case 'icon':
                        break;
                    case 'news':
                        $media = News::findOrFail($id);
                        if ($media->image) {
                            deleteFile($media->image);
                            $media->update([
                                'image' => null
                            ]);
                        }
                        break;
                }
            }
            if ($response) {
                flash($type . ' Removed', 'success');
                return \redirect()->back();
            } else {
                return \response()->json([
                    'message' => $type . ' Removed!'
                ], JsonResponse::HTTP_OK);
            }
        } catch (ModelNotFoundException $e) {
            if ($response) {
                flash($e->getMessage(), 'danger');
                return \redirect()->back();
            } else {
                return \response()->json([
                    'message' => $e->getMessage()
                ], JsonResponse::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            if ($response) {
                flash($e->getMessage(), 'danger');
                return \redirect()->back();
            } else {
                return \response()->json([
                    'message' => $e->getMessage()
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function changeMedia(MediaRequest $request, $id, $type)
    {
        try {
            $image_type = $type;
            switch ($type) {
                case 'avatar':
                    $user = User::findOrFail($id);
                    if ($request->avatar != null) {
                        if ($request->hasFile('avatar')) {
                            deleteFile($user->avatar);
                        }
                    }
                    $user->update([
                        'avatar' => $request->avatar
                    ]);
                    break;
                case 'image':
                    $image_type = $request->type;
                    $width = null;
                    $height = null;
                    switch ($image_type) {
                        case 'logo':
                            $logo = config()->get('image.media.logo');
                            $width = $logo['width'];
                            $height = $logo['height'];
                            break;
                        case 'thumbnail':
                            $thumbnail = config()->get('image.media.thumbnail');
                            $width = $thumbnail['width'];
                            $height = $thumbnail['height'];
                            break;
                        case 'banner':
                            $banner = config()->get('image.media.banner');
                            $width = $banner['width'];
                            $height = $banner['height'];
                            break;
                        case 'secondaryBanner':
                            $secondaryBanner = config()->get('image.media.secondaryBanner');
                            $width = $secondaryBanner['width'];
                            $height = $secondaryBanner['height'];
                            break;
                    }
                    $business = Business::findOrFail($id);
                    $extension = $request->file('image')->extension();
                    $filePath = saveResizeImage($request->image, "business/{$request->type}", $width, $height, $extension);
                    $media = $business->media()->where('type', $request->type)->first();
                    if ($media) {
                        \deleteFile($media->path);
                        $media->update([
                            'path' => $filePath,
                            'size' => $request->file('image')->getSize(),
                            'mime_type' => $extension,
                            'type' => $request->type
                        ]);
                    } else {
                        $business->media()->create([
                            'path' => $filePath,
                            'size' => $request->file('image')->getSize(),
                            'mime_type' => $extension,
                            'type' => $request->type
                        ]);
                    }
                    break;
            }
            flash($image_type . ' updated succesfully', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    public function businessOptionalMedia(Request $request, $id)
    {
        $image = config()->get('image.media.banner');
        $imageKeys = array_keys($image);
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];
        $validator = Validator::make($request->all(), [
            'file.*' => [
                'mimes:jpeg,png,jpg',
                "max:$size",
                "dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height",
                new CheckProductMediaCount('business')
            ],
        ], [
            'file.*.mimes' => 'Only jpeg, png, jpg files can be uploaded!',
            'file.*.max' => 'Maximum file size should be' . $size . ' kbs',
            'file.*.dimensions' => 'Image has invalid dimensions',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first()
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            DB::beginTransaction();
            $files = $request->file;
            $business = Business::findOrFail($id);
            foreach ($files as $key => $value) {
                $extension = $value->extension();
                $filePath = saveResizeImage($value, "business/banner", $width, $height, $extension);
                $media = $business->media()->create([
                    'path' => $filePath,
                    'size' => $value->getSize(),
                    'mime_type' => $value->extension(),
                    'type' => 'banner'
                ]);
            }
            $business = Business::with('secondaryImages')->findOrFail($id);
            DB::commit();
            return \response()->json([
                'media' => $business->secondaryImages,
                'message' => 'Business image added!'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return \response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteBusinessOptionalMedia($uuid, $id)
    {
        try {
            $media = Media::findOrFail($id);
            deleteFile($media->path);
            $media->delete();
            $business = Business::whereUuid($uuid)->withCount('secondaryImages')->firstOrFail();
            $count = $business->secondary_images_count;
            return \response()->json([
                'count' => $count,
                'message' => 'Product image Removed!'
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return \response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return \response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
