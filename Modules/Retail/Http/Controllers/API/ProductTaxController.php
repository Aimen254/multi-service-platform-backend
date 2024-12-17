<?php

namespace Modules\Retail\Http\Controllers\API;

use App\Transformers\TaxTransformer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Modules\Retail\Http\Requests\TaxRequest;

class ProductTaxController extends Controller
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
                    'product' =>  (new TaxTransformer)->transform($this->product),
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
    public function store(TaxRequest $request, $module, $uuid): JsonResponse
    {
        try {
            DB::beginTransaction();

            $this->product->update($request->all());

            DB::commit();
            return response()->json(['message' => "Product Tax information updated successfully."], 200);
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
