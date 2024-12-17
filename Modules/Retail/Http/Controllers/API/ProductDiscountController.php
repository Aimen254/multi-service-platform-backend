<?php

namespace Modules\Retail\Http\Controllers\API;

use App\Transformers\DiscountTransformer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Modules\Retail\Http\Requests\ProductDiscountRequest;

class ProductDiscountController extends Controller
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
    public function index($module, $uuid): JsonResponse|string
    {
        try {
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => [
                    'product' => (new DiscountTransformer)->transform($this->product),
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
    public function store(ProductDiscountRequest $request, $module, $uuid): JsonResponse
    {
        try {
            DB::beginTransaction();
            $price = $this->product->price;
            $discount = null;
            if ($request->input('discount_type') ) {
                if ($request->discount_type == 'percentage') {
                    $discount_percentage = min($request->discount_value, 100);
                    $discount = $price * (1 - $discount_percentage / 100);
                } else if ($request->discount_type == 'fixed') {
                    $discount = $price - $request->discount_value;
                }
                $request->merge(['discount_price' => $discount]);
                $this->product->update($request->all());
            } else {
                $this->product->update([
                    'discount_price' => null,
                    'discount_start_date' => null,
                    'discount_end_date' => null,
                    'discount_type' => null,
                    'discount_value' => null
                ]);
            }
            DB::commit();
            return response()->json(['message' => "Product Discount information updated successfully."], 200);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            return response()->json(['status' => JsonResponse::HTTP_NOT_FOUND, 'message' => $exception->getMessage()], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR, 'message' => $exception->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
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
    public function destroy()
    {
        //
    }
}
