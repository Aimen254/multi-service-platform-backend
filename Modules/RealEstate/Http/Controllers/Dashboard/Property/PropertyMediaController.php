<?php

namespace Modules\RealEstate\Http\Controllers\Dashboard\Property;

use Inertia\Inertia;
use App\Models\Media;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Rules\CheckProductMediaCount;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PropertyMediaController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $uuid)
    {
        $mediaSizes = \config()->get('realestate.media.property');
        $product = Product::with(['secondaryImages', 'mainImage'])->whereUuid($uuid)->firstOrFail();
        return Inertia::render('RealEstate::Property/Settings/PropertyMedia', [
            'product' => $product,
            'mediaSizes' => $mediaSizes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request, $moduleId, $uuid)
    {
        $image = config()->get('realestate.media.property');
        $imageKeys = array_keys($image);
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];
        $validator = Validator::make($request->all(), [
            'file.*' => [
                'mimes:jpeg,png,jpg',
                "max:$size",
                "dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height",
                new CheckProductMediaCount('product')
            ],
        ], [
            'file.*.mimes' => 'Only jpeg, png, jpg files can be uploaded!',
            'file.*.max' => 'Maximum file size should be ' . $size . ' kbs',
            'file.*.dimensions' => 'Image has invalid dimensions',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first()
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $files = $request->file;
            DB::beginTransaction();
            $product = Product::whereUuid($uuid)->firstOrFail();
            $items = array();
            foreach ($files as $key => $value) {
                $extension = $value->extension();
                $filePath = saveResizeImage($value, "products", $width, $height,  $extension);
                $media = $product->media()->create([
                    'path' => $filePath,
                    'size' => $value->getSize(),
                    'mime_type' => $value->extension(),
                    'type' => 'image'
                ]);
            }
            $product = Product::with(['secondaryImages'])->whereUuid($uuid)->firstOrFail();
            DB::commit();
            return \response()->json([
                'media' => $product->secondaryImages,
                'message' => 'Property image added!'
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
        $image = config()->get('realestate.media.property');
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
            $extension = $request->file('image')->extension();
            $filePath = saveResizeImage($request->image, "products", $width, $height,  $extension);
            if ($id != 0) {
                $media = Media::findOrFail($id);
                \deleteFile($media->path);
                $media->update([
                    'path' => $filePath,
                    'size' => $request->file('image')->getSize(),
                    'mime_type' => $extension,
                    'type' => 'image',
                    'is_external' => 0
                ]);
            } else {
                $product = Product::whereUuid($uuid)->firstOrFail();
                $media = $product->media()->create([
                    'path' => $filePath,
                    'size' => $request->file('image')->getSize(),
                    'mime_type' => $extension,
                    'type' => 'image',
                    'is_external' => 0
                ]);
            }
            flash('Property main image updated successfully.', 'success');
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
     * @param int $id
     * @return Renderable
     */
    public function destroy($moduleId, $uuid, $id)
    {
        try {
            $media = Media::findOrFail($id);
            deleteFile($media->path);
            $media->delete();
            $product = Product::whereUuid(request()->uuid)->withCount('secondaryImages')->firstOrFail();
            $count = $product->secondary_images_count;
            return \response()->json([
                'count' => $count,
                'message' => 'Property image Removed!'
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
