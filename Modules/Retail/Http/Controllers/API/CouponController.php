<?php

namespace Modules\Retail\Http\Controllers\API;

use Modules\Retail\Http\Requests\CouponRequest;
use App\Models\Business;
use App\Models\Coupon;
use App\Transformers\CouponTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request, $businessUuid)
    {
        $limit = $request->input('limit') ? $request->input('limit') : \config()->get('settings.pagination_limit');
        $coupons = Coupon::where(function ($query) {
            if (request()->keyword) {
                $keyword = request()->keyword;
                $query->where('code', 'like', '%' . $keyword . '%');
            }
        })->where('model_type', 'App\Models\Business')
            ->whereRelation('model', 'uuid', $businessUuid)
            ->orderBy('id', 'desc')->paginate($limit);
        $paginate = apiPagination($coupons, $limit);
        $coupons = (new CouponTransformer)->transformCollection($coupons, ['store_cpupon' => true]);
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'data' => $coupons,
            'meta' => $paginate,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(CouponRequest $request, $businessUuid)
    {
        try {
            $business = Business::where('uuid', $businessUuid)->firstOrFail();
            $business->coupons()->create([
                'code' => $request->code,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'coupon_type' => $request->coupon_type,
                'created_by' => auth()->user()->id,
            ]);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Coupon created successfully.',
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(CouponRequest $request, $businessUuid, $id)
    {
        try {
            $coupon = Coupon::findOrfail($id);
            $coupon->update([
                'code' => $request->code,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'coupon_type' => $request->coupon_type,
            ]);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Coupon updated successfully.',
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($businessUuid, $id)
    {
        try {
            $coupon = Coupon::findOrFail($id);
            foreach ($coupon->carts as $cart) {
                $cart->update([
                    'coupon_id'  => null
                ]);
            }
            $coupon->delete();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Coupon deleted successfully.',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Unable to find this coupon'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeStatus($businessUuid, $id)
    {
        try {
            $coupon = Coupon::findOrFail($id);
            $coupon->statusChanger()->save();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Coupon status changed succesfully.',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Unable to find this coupon'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
