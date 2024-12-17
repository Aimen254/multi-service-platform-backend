<?php

namespace Modules\Classifieds\Http\Controllers\Dashboard\Classifieds;

use App\Models\Media;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Classifieds\Http\Requests\ClassifiedMediaRequest;

class ClassifiedMediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param int $moduleId
     * @param string $uuid
     * @return Renderable
     */
    public function index($moduleId, $uuid)
    {
        $mediaSizes = \config()->get('classifieds.media.classified');
        $classified = Product::with(['secondaryImages', 'mainImage'])->whereUuid($uuid)->firstOrFail();
        return inertia('Classifieds::Classifieds/Settings/ClassifiedMedia', [
            'classified' => $classified,
            'mediaSizes' => $mediaSizes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ClassifiedMediaRequest $request
     * @param int $moduleId
     * @param string $uuid
     * @return Renderable
     */
    public function store(ClassifiedMediaRequest $request, $moduleId, $uuid)
    {
        $image = \config()->get('classifieds.media.classified');
        $width = $image['width'];
        $height = $image['height'];

        try {
            $files = $request->file;
            DB::beginTransaction();
            $classified = Product::whereUuid($uuid)->firstOrFail();
            $items = array();
            foreach ($files as $key => $value) {
                $extension = $value->extension();
                $filePath = saveResizeImage($value, "products", $width, $height,  $extension);
                $media = $classified->media()->create([
                    'path' => $filePath,
                    'size' => $value->getSize(),
                    'mime_type' => $value->extension(),
                    'type' => 'image'
                ]);
            }
            $classified = Product::with(['secondaryImages'])->whereUuid($uuid)->firstOrFail();
            DB::commit();
            return \response()->json([
                'media' => $classified->secondaryImages,
                'message' => 'Image added!'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return \response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $moduleId
     * @param string $uuid
     * @param int|null $id
     * @return Renderable
     */
    public function update(Request $request, $moduleId, $uuid, $id = null)
    {
        $image = \config()->get('classifieds.media.classified');
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
            if ($id != 0) {
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
                $classified = Product::whereUuid($uuid)->firstOrFail();
                $media = $classified->media()->create([
                    'path' => saveResizeImage($request->image, 'products', $width, $height, $request->image->extension()),
                    'size' => $request->image->getSize(),
                    'mime_type' => $request->image->extension(),
                    'type' => 'image',
                    'is_external' => 0
                ]);
            }
            flash('The main image updated successfully.', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $moduleId
     * @param string $uuid
     * @param int $id
     * @return Renderable
     */
    public function destroy($moduleId, $uuid, $id)
    {
        try {
            $media = Media::findOrFail($id);
            deleteFile($media->path);
            $media->delete();
            $news = Product::whereUuid(request()->uuid)->withCount('secondaryImages')->firstOrFail();
            $count = $news->secondary_images_count;
            return \response()->json([
                'count' => $count,
                'message' => 'Image Removed!'
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
