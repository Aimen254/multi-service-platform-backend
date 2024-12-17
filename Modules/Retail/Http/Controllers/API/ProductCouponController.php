<?php

namespace Modules\Retail\Http\Controllers\API;

use App\Models\Coupon;
use App\Transformers\CouponsListTransformer;
use App\Transformers\CouponTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Modules\Retail\Http\Requests\ProductCouponRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductCouponController extends Controller
{

    protected $product;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->product = Product::whereUuid(Route::current()->parameters['uuid'])->firstOrFail();
    }

    /**
     * Display a listing of the resource.
     */
    public function index($module, $uuid)
    {
        try {
            $limit = request()->limit;
            $coupons = $this->product->coupons()->paginate($limit);
            $paginate = apiPagination($coupons, $limit);
            $codesList =  Coupon::select('id','code')->where(function ($query) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->where('code', 'like', '%' . $keyword . '%');
                }
            })
                ->where('model_id', $this->product->business_id)
                ->where('coupon_type', 'product')
                ->whereDoesntHave('products', function ($q) {
                    $q->where('product_id', $this->product->id);
                })->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => [
                    'product' => $this->product,
                    'couponsList' => (new CouponsListTransformer)->transformCollection($coupons),
                    'codesList' => (new CouponTransformer)->transformCollection($codesList),
                    'meta' => $paginate,
                ],
            ], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCouponRequest $request, $module, $uuid): JsonResponse
    {
        try {
            $this->product->coupons()->syncWithoutDetaching($request->coupon_id);

            return response()->json([
                'message' => 'Coupon assigned successfully.',
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Unable to find this variant.'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show()
    {
       //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
       //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
       //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($module, $uuid, $id): JsonResponse
    {
        try {
            $this->product->coupons()->detach($id);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Coupon deleted successfully.',
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

    public function changeStatus($moduleId, $uuid, $id)
    {
        try {
            $product = Coupon::findOrFail($id)->products()->wherePivot('product_id', $this->product->id)->firstOrFail();
            $coupon = $product->coupons()->first();
            if ($coupon->status == 'inactive') {
                return response()->json([
                    'status' => JsonResponse::HTTP_NOT_FOUND,
                    'message' => 'Status not changed! The main coupon status is inactive'
                ], JsonResponse::HTTP_NOT_FOUND);
            }
            $status = $product->pivot->status == 'active' ? 'inactive' : 'active';
            $attribute = ['status' => $status, 'previous_status' => $product->pivot->status];

            // Use sync method to update the pivot table
            $this->product->coupons()->sync([$id => $attribute], false);

            return response()->json([
                'message' => "Status updated successfully!"
            ], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $exception->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
