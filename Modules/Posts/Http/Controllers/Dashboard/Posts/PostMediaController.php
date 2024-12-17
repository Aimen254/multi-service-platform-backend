<?php

namespace Modules\Posts\Http\Controllers\Dashboard\Posts;

use App\Models\Media;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use Modules\Posts\Http\Requests\PostMediaRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PostMediaController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $uuid)
    {
        $mediaSizes = \config()->get('posts.media.posts');
        $post = Product::with(['secondaryImages', 'mainImage'])->whereUuid($uuid)->firstOrFail();
        return inertia('Posts::Posts/Settings/PostsMedia', [
            'post' => $post,
            'mediaSizes' => $mediaSizes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param PostMediaRequest $request
     * @return Renderable
     */
    public function store(PostMediaRequest $request, $moduleId, $uuid)
    {
        $image = \config()->get('posts.media.posts');
        $width = $image['width'];
        $height = $image['height'];

        try {
            $files = $request->file;
            DB::beginTransaction();
            $post = Product::whereUuid($uuid)->firstOrFail();
            $items = array();
            foreach ($files as $key => $value) {
                $extension = $value->getClientOriginalExtension();
                $filePath = saveResizeImage($value, "products", $width, $height,  $extension);
                $media = $post->media()->create([
                    'path' => $filePath,
                    'size' => $value->getSize(),
                    'mime_type' => $value->getClientOriginalExtension(),
                    'type' => 'image'
                ]);
            }
            $post = Product::with(['secondaryImages'])->whereUuid($uuid)->firstOrFail();
            DB::commit();
            return \response()->json([
                'media' => $post->secondaryImages,
                'message' => 'Vehicle image added!'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return \response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $moduleId, $uuid, $id = null)
    {
        $image = \config()->get('posts.media.posts');
        $imageKeys = array_keys($image);
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];

        $request->validate([
            'image' => [
                'mimes:jpeg,png,jpg',
                "max:$size",
                "dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height",
            ]
        ]);
        try {
            if ($id != 0 && $id != null) {
                $media = Media::findOrFail($id);
                \deleteFile($media->path);
                $media->update([
                    'path' => saveResizeImage($request->image, 'products', $width, $height, $request->image->extension()),
                    'size' => $request->image->getSize(),
                    'mime_type' => $request->image->extension(),
                    'type' => 'image',
                    'is_external' => 0
                ]);
            } else {
                $post = Product::whereUuid($uuid)->firstOrFail();
                $media = $post->media()->create([
                    'path' => saveResizeImage($request->image, 'products', $width, $height, $request->image->extension()),
                    'size' => $request->image->getSize(),
                    'mime_type' => $request->image->extension(),
                    'type' => 'image',
                    'is_external' => 0
                ]);
            }
            flash('Post main image updated successfully.', 'success');
            return \back();
        } catch (ModelNotFoundException $e) {
            flash($e->getMessage(), 'danger');
            return \back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \back();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($moduleId, $uuid, $id)
    {
        try {
            $media = Media::findOrFail($id);
            deleteFile($media->path);
            $media->delete();
            $post = Product::whereUuid(request()->uuid)->withCount('secondaryImages')->firstOrFail();
            $count = $post->secondary_images_count;
            return \response()->json([
                'count' => $count,
                'message' => 'Post image Removed!'
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
