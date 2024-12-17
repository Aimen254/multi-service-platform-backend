<?php

namespace Modules\Recipes\Http\Controllers\Dashboard\Recipes;

use App\Models\Media;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use Modules\Recipes\Http\Requests\RecipesMediaRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RecipesMediaController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($module, $uuid)
    {
        $mediaSizes = \config()->get('recipes.media.recipes');
        $recipe = Product::with(['secondaryImages', 'mainImage'])->whereUuid($uuid)->firstOrFail();
        return inertia('Recipes::Recipes/Settings/RecipesMedia', [
            'recipes' => $recipe,
            'mediaSizes' => $mediaSizes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('recipes::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(RecipesMediaRequest $request, $moduleId, $uuid)
    {
        $image = \config()->get('recipes.media.recipes');
        $width = $image['width'];
        $height = $image['height'];
        try {
            $files = $request->file;
            DB::beginTransaction();
            $recipes = Product::whereUuid($uuid)->firstOrFail();
            $items = array();
            foreach ($files as $key => $value) {
                $extension = $value->extension();
                $filePath = saveResizeImage($value, "products", $width, $height,  $extension);
                $media = $recipes->media()->create([
                    'path' => $filePath,
                    'size' => $value->getSize(),
                    'mime_type' => $value->extension(),
                    'type' => 'image'
                ]);
            }
            $recipe = Product::with(['secondaryImages'])->whereUuid($uuid)->firstOrFail();
            DB::commit();
            return \response()->json([
                'media' => $recipe->secondaryImages,
                'message' => 'Recipes image added!'
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
        return view('recipes::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('recipes::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $moduleId, $uuid, $id = null)
    {
        $image = \config()->get('recipes.media.recipes');
        $imageKeys = array_keys($image);
        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];
        $directory = 'products';

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
                    'path' => saveResizeImage($request->image, $directory, $width, $height, $request->image->extension()),
                    'size' => $request->image->getSize(),
                    'mime_type' => $request->image->extension(),
                    'type' => 'image',
                    'is_exteranl' => 0
                ]);
            } else {
                $news = Product::whereUuid($uuid)->firstOrFail();
                $media = $news->media()->create([
                    'path' => saveResizeImage($request->image, $directory, $width, $height, $request->image->extension()),
                    'size' => $request->image->getSize(),
                    'mime_type' => $request->image->extension(),
                    'is_exteranl' => 0,
                    'type' => 'image'
                ]);
            }
            flash('Recipes main image updated successfully.', 'success');
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
            $news = Product::whereUuid(request()->uuid)->withCount('secondaryImages')->firstOrFail();
            $count = $news->secondary_images_count;
            return \response()->json([
                'count' => $count,
                'message' => 'Recipes image Removed!'
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
