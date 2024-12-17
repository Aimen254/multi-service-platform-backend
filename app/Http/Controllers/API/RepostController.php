<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\RepostRequest;
use App\Models\Product;
use App\Models\PublicProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RepostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RepostRequest $request)
    {

        $product = Product::findOrFail($request->product['id']);
        $profile_id=$request->profile_id;
        $user_id=Auth::id();
        try {

            if(!$profile_id){
                return response()->json([
                    'status' => JsonResponse::HTTP_BAD_REQUEST,
                    'message' => "please select persona first."
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            $product->reposts()->sync([
                $profile_id => ['user_id' => $user_id]
            ], false);

            Log::info("Product {$product->id} reposted to public profile {$profile_id}.");
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'post'=>$product,
                'message'=>'post reposted!'
            ], JsonResponse::HTTP_OK);
            return response()->json(['message' => 'Product reposted successfully.'], 200);
        } catch (\Exception $e) {
            Log::error("Failed to repost product {$product->id} to public profile {$profile_id}: " . $e->getMessage());

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
    public function destroy($id)
    {
        //
    }
}
