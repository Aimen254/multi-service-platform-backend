<?php

namespace Modules\Government\Http\Controllers\API;

use App\Models\Product;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;

class GovernmentPostReviewController extends Controller
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
        return view('government::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store($request)
    {
        $type = $request->input('type');
        if ($type == 'business') {
            $model = Business::findOrFail($request->model_id);
        } else if ($type == 'product') {
            $model = Product::findOrFail($request->model_id);
        }
        //Saving data in database
        $model->reviews()->updateOrCreate(['id' => $request->input('id')], [
            'user_id' => $request->user_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        return;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('government::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('government::edit');
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
