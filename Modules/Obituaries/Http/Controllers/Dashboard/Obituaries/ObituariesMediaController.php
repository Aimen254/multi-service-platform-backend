<?php

namespace Modules\Obituaries\Http\Controllers\Dashboard\Obituaries;

use App\Models\Media;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use Modules\Obituaries\Http\Requests\ObituariesMediaRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ObituariesMediaController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $uuid)
    {
        $mediaSizes = \config()->get('obituaries.media.obituaries');
        $obituaries = Product::with(['secondaryImages', 'mainImage'])->whereUuid($uuid)->firstOrFail();
        return inertia('Obituaries::Obituaries/Settings/ObituariesMedia', [
            'obituaries' => $obituaries,
            'mediaSizes' => $mediaSizes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('obituaries::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(ObituariesMediaRequest $request, $moduleId, $uuid)
    {
        $image = \config()->get('obituaries.media.obituaries');
        $width = $image['width'];
        $height = $image['height'];

        try {
            $files = $request->file;
            DB::beginTransaction();
            $obituaries = Product::whereUuid($uuid)->firstOrFail();
            $items = array();
            foreach ($files as $key => $value) {
                $extension = $value->extension();
                $filePath = saveResizeImage($value, "products", $width, $height,  $extension);
                $media = $obituaries->media()->create([
                    'path' => $filePath,
                    'size' => $value->getSize(),
                    'mime_type' => $value->extension(),
                    'type' => 'image'
                ]);
            }
            $obituaries = Product::with(['secondaryImages'])->whereUuid($uuid)->firstOrFail();
            DB::commit();
            return \response()->json([
                'media' => $obituaries->secondaryImages,
                'message' => 'Obituaries image added!'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return \response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('obituaries::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('obituaries::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $moduleId, $uuid, $id = null)
    {
        $image = \config()->get('obituaries.media.obituaries');
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
                $obituaries = Product::whereUuid($uuid)->firstOrFail();
                $media = $obituaries->media()->create([
                    'path' => saveResizeImage($request->image, 'products', $width, $height, $request->image->extension()),
                    'size' => $request->image->getSize(),
                    'mime_type' => $request->image->extension(),
                    'type' => 'image',
                    'is_external' => 0
                ]);
            }
            flash('Obituaries main image updated successfully.', 'success');
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
            $obituaries = Product::whereUuid(request()->uuid)->withCount('secondaryImages')->firstOrFail();
            $count = $obituaries->secondary_images_count;
            return \response()->json([
                'count' => $count,
                'message' => 'Obituaries image Removed!'
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
