<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Transformers\VehicleTransformer;
use Modules\Automotive\Entities\ProductAutomotive;
use Modules\Automotive\Http\Requests\VehicleRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $moduleId)
    {
        try {
            $limit = $request->input('limit') ? $request->input('limit') : \config()->get('settings.pagination_limit');
            $vehicles = ProductAutomotive::with('product', 'model', 'maker')->paginate($limit);
            $paginate = apiPagination($vehicles, $limit);
            $allVehicles = (new VehicleTransformer)->transformCollection($vehicles);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $allVehicles,
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VehicleRequest $request, $moduleId)
    {
        try {
            DB::beginTransaction();
            $levelThreeTag = StandardTag::findOrFail($request->level_three_tag);
            $levelFourTag = StandardTag::findOrFail($request->level_four_tags[0]['id']);
            $request->merge([
                'business_id' => $request->business_id,
                'name' => $levelThreeTag->name . ' ' . $levelFourTag->name,
                'maker_id' => $levelThreeTag->id,
                'model_id' => $levelFourTag->id
            ]);
            $product = Product::create($request->all());
            $product->vehicle()->create($request->all());
            DB::commit();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Vehicle Added Successfully.',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($moduleId, $id)
    {
        try {
            $vehicle = ProductAutomotive::with('product', 'model', 'maker')->findOrFail($id);
            $vehicle = (new VehicleTransformer)->transform($vehicle);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $vehicle,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleRequest $request, $moduleId, $productUuid)
    {
        try {
            DB::beginTransaction();
            $product = Product::whereUuid($productUuid)->firstOrFail();
            $discount = null;
            if ($product->discount_value) {
                if ($product->discount_type == 'percentage') {
                    $discount = ($request->price * $product->discount_value) / 100;
                    $discount = $request->price - $discount;
                } else {
                    if ($request->price > $product->discount_value) {
                        $discount = $request->price - $product->discount_value;
                    }
                }
            }
            $levelThreeTag = StandardTag::findOrFail($request->level_three_tag);
            $levelFourTag = StandardTag::findOrFail($request->level_four_tags[0]['id']);
            $request->merge([
                'discount_price' => numberFormat($discount),
                'name' => $levelThreeTag->name . ' ' . $levelFourTag->name,
                'maker_id' => $levelThreeTag->id,
                'model_id' => $levelFourTag->id
            ]);
            $product->update($request->all());
            $product->vehicle()->update([
                'type' => $request->type,
                'trim' => $request->trim,
                'year' => $request->year,
                'mpg' => $request->mpg,
                'stock_no' => $request->stock_no,
                'vin' => $request->vin,
                'sellers_notes' => $request->sellers_notes,
                'mileage' => $request->mileage,
                'maker_id' => $request->maker_id,
                'model_id' => $request->model_id
            ]);
            DB::commit();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Vehicle Updated Successfully.',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($moduleId, $productUuid)
    {
        try {
            $product = Product::where('uuid', $productUuid)->firstOrfail();
            $product->delete();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Vehicle Deleted Successfully.',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
