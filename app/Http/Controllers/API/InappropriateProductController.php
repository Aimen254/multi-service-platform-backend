<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Requests\API\InappropriateProductRequest;
use App\Models\InappropriateProduct;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InappropriateProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\InappropriateProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InappropriateProductRequest $request)
    {
        try {
            $response = $this->destroy($request->all());
            if (!empty($response) && count($response) > 0) {
                return $response;
            }

            $product = Product::findOrFail($request->product_id);
            $data = $product->inappropriateProducts()->create(array_merge($request->validated(), [
                'user_id' => auth()->id(),
                'type' => $request->type
            ]));

            if ($request->type == 'flag') {
                return response()->json([
                    'status' => true,
                    'data' => $data,
                    'flagItem' => true,
                    'message' => 'Flagged successfully',
                ], JsonResponse::HTTP_OK);
            } else {
                return response()->json([
                    'status' => true,
                    'data' => $data,
                    'hideItem' => true,
                    'message' => 'Hide successfully',
                ], JsonResponse::HTTP_OK);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
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
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($request)
    {
        $inappropriateProduct = InappropriateProduct::where('user_id', auth()->id())
            ->where('model_id', $request['product_id'])->where('type', $request['type'])->first();

        if ($inappropriateProduct) {
            $inappropriateProduct->delete();
            $message = $request['type'] == 'flag'
                ? 'Removed from flagged successfully.'
                : 'Unhided successfully.';
            return ['message' => $message, 'flagItem' => false];
        }
    }
}
