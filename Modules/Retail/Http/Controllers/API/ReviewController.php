<?php

namespace Modules\Retail\Http\Controllers\API;

use Inertia\Inertia;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Business;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;



class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($businessUuid)
    {
        $limit = request()->input('limit') ? request()->input('limit') : config('settings.pagination_limit');
        $business = Business::with(['banner', 'logo', 'thumbnail'])->findOrFail(getBusinessDetails($businessUuid)->id);
        $reviews = $business->reviews()->whereHas('user', function ($query) {
            if (request()->input('keyword')) {
                $keyword = request()->keyword;
                $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', ["%{$keyword}%"]);
            }
        })->with('user')->orderBy('id', 'desc');
        if ($keyword = request()->input('keyword')) {
            $reviews->where(function ($query) use ($keyword) {
                $query->whereHas('user', function ($query) use ($keyword) {
                    $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', ["%{$keyword}%"]);
                });
            });
        }
        $reviews = $reviews->with('user')->paginate($limit);
        $paginate = apiPagination($reviews, $limit);
        return response()->json([
            'reviews' => $reviews->items(),
            'meta' =>  $paginate
        ]);
    }



    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('retail::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('retail::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('retail::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request,  $businessEmailId, $id)
    {
        try {

            $review = Review::findOrFail($id);
            $review->delete();
            return response()->json([
                'data' =>   $review,
                'message' => 'Reviews Deleted!'
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Unable to find this Business Holiday'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * change the specified resource status.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */



    public function changeStatus($businessUuid, $id)
    {
        try {
            DB::beginTransaction();
            $review = Review::findOrfail($id);
            $review->statusChanger()->save();
            DB::commit();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $review,
                'message' => 'Status changed!'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
