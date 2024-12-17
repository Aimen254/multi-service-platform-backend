<?php

namespace Modules\Automotive\Http\Controllers\API;

use App\Models\StandardTag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use App\Transformers\DreamCarTransformer;
use Modules\Automotive\Entities\DreamCar;
use Illuminate\Contracts\Support\Renderable;
use Modules\Automotive\Http\Requests\DreamCarRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DreamCarController extends Controller
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
            $module = StandardTag::where('slug', 'automotive')->first();
            $dreamCar = request()->user()->dreamCars()->where('module_id', $module->id)->with(['maker', 'model'])->latest()->paginate($limit);
            $paginate = apiPagination($dreamCar, $limit);
            $dreamCars = (new DreamCarTransformer)->transformCollection($dreamCar);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $dreamCars,
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
        return view('automotive::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(DreamCarRequest $request)
    {
        try {
            $user = request()->user();
            $module = StandardTag::where('slug', $request->module)->firstOrFail();
            $request->merge([
                'module_id' => $module->id
            ]);
            $car = null;
            $message = 'Dream car added successfully';
            if (!$user->dreamCars()->where('make_id', $request->make_id)
                ->where('module_id', $request->module_id)
                ->where('model_id', $request->model_id)
                ->where('from', $request->from)->where('to', $request->to)
                ->exists()) {
                $car = $user->dreamCars()->create($request->all());
            } else {
                $message = 'Dream car updated successfully';
            }
            $dreamCar = $car ? (new DreamCarTransformer)->transform($car) : null;
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $message,
                'data' => $dreamCar,
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
        return view('automotive::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('automotive::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(DreamCarRequest $request, $id)
    {
        try {
            $car = DreamCar::findOrFail($id);
            $car->update($request->all());
            $dreamCar = (new DreamCarTransformer)->transform($car);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Dream car updated successfully',
                'data' => $dreamCar,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Dream car not found.'
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
                'message' => 'Dream car deleted successfully',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Dream car not found.'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
