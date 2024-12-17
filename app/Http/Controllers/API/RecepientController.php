<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Recepient;
use Illuminate\Http\Request;
use App\Http\Requests\API\RecipientRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Transformers\RecepientTransformer;

class RecepientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $limit = request()?->limit ?? \config()->get('settings.pagination_limit');
            $user = auth('sanctum')->user();
            $recepients = $user->recepients();
            $recepients = $recepients->paginate($limit);
            $paginate = apiPagination($recepients);
            $recepients = (new RecepientTransformer)->transformCollection($recepients);

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $recepients,
                'meta' => $paginate
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
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
    public function store(RecipientRequest $request)
    {
        try {
            $message = $request->input('id') ? "Recipient updated successfully" : "Recipient created successfully";
            DB::beginTransaction();
            $user = auth('sanctum')->user();
            $request->merge([
                'user_id' => $user->id,
                'status' => 'active'
            ]);
            $result = $user->recepients()->create($request->validated());
            $latestRecepients = $user->recepients()->latest()->where('status', 'active')->first();

            DB::commit();
            // $address = (new AddressTransformer)->transformCollection($latestAddress);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $message,
                'data' => $latestRecepients,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Recepient  $recepient
     * @return \Illuminate\Http\Response
     */
    public function show(Recepient $recepient)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Recepient  $recepient
     * @return \Illuminate\Http\Response
     */
    public function edit(Recepient $recepient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Recepient  $recepient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Recepient $recepient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Recepient  $recepient
     * @return \Illuminate\Http\Response
     */
    public function destroy(Recepient $recepient)
    {
        //
    }
}
