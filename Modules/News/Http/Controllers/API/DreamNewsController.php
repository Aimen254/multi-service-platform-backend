<?php

namespace Modules\News\Http\Controllers\API;

use App\Models\StandardTag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use App\Transformers\DreamCarTransformer;
use Modules\Automotive\Entities\DreamCar;
use Illuminate\Contracts\Support\Renderable;
use Modules\News\Http\Requests\DreamNewsRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DreamNewsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        try {
            $limit = request()->input('limit')
                ? request()->limit : \config()->get('settings.pagination_limit');
            $module = StandardTag::where('slug', 'news')->first();
            $dreamNews = request()->user()->dreamCars()->where('module_id', $module->id)->with(['maker', 'model'])->latest()->paginate($limit);
            $paginate = apiPagination($dreamNews, $limit);
            $options = [
                'module' => $module->slug
            ];
            $dreamNews = (new DreamCarTransformer)->transformCollection($dreamNews, $options);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $dreamNews,
                'meta' => $paginate,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('news::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(DreamNewsRequest $request)
    {
        try {
            $user = request()->user();
            $module = StandardTag::where('id', $request->module)->orWhere('slug', $request->module)->first();
            if ($module) {
                $request->merge([
                    'module_id' => $module->id
                ]);
            }
            $news = null;
            $message = 'Dream news added successfully';
            if (!$user->dreamCars()->where('make_id', $request->make_id)
                ->where('module_id', $request->module_id)
                ->where('model_id', $request->model_id)
                ->exists()) {
                $news = $user->dreamCars()->create($request->all());
            } else {
                $message = 'Dream news updated successfully';
            }
            $options = [
                'module' => $module->slug
            ];
            $dreamNews = $news ? (new DreamCarTransformer)->transform($news, $options) : null;
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $message,
                'data' => $dreamNews,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
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
        return view('news::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('news::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(DreamNewsRequest $request, $id)
    {
        try {
            $news = DreamCar::findOrFail($id);
            $news->update($request->all());
            $options = [
                'module' => 'news'
            ];
            $dreamCar = (new DreamCarTransformer)->transform($news, $options);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Dream news updated successfully',
                'data' => $dreamCar,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Dream news not found.'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            $dreamCar = DreamCar::findOrFail($id);
            $dreamCar->delete();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Dream news deleted successfully',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Dream news not found.'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
