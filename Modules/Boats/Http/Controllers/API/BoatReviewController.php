<?php

namespace Modules\Boats\Http\Controllers\API;

use App\Models\Review;
use App\Models\Business;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\StandardTag;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Support\Renderable;

class BoatReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($request, $limit, $type)
    {
        $id = $request->input('id');
        if ($type == 'business') {
            $model = Business::findOrFail($id);
        }
        if ($type == 'product') {
            $model = Product::findOrFail($id);
        }
        $review = $model->reviews();
        if (isset($request->order) && $request->order != 'null') {
            $review->orderBy('rating', $request->order);
        }
        $review = $review->paginate($limit);
        return $review;
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('boats::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return string
     */
    public function store($request): string
    {
        $type = $request->input('type');
        $model = null;

        switch ($type) {
            case 'business':
                $model = Business::findOrFail($request->model_id);
                break;
            case 'product':
                $model = Product::findOrFail($request->model_id);
                break;
        }

        $review = $model->reviews()->updateOrCreate(
            ['user_id' => $request->user_id],
            ['rating' => $request->rating, 'comment' => $request->comment]
        );

        if ($review->wasRecentlyCreated) {
            return 'Review Added successfully.';
        } else {
            return 'Review Updated successfully.';
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('boats::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('boats::edit');
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
    public function destroy($id)
    {
        //
    }
}
