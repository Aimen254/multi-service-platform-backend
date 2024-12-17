<?php

namespace Modules\Recipes\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;

class RecipesReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($request, $limit, $type)
    {
        $id = $request->input('id');
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
        return view('recipes::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store($request, $moduleId)
    {
        $message = '';
        $type = $request->input('type');
        $model = $type === 'user' ? User::findOrFail($request->model_id) : Product::findOrFail($request->model_id);
        $moduleId = $type === 'user' ? $moduleId : null;

        //Saving data in database
        $review = $model->reviews()->updateOrCreate(
            [
                'user_id' => $request->user_id,
            ],
            [
                'order_id' => $request->order_id,
                'user_id' => $request->user_id,
                'module_id' => $moduleId,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );

        if($review->wasRecentlyCreated) {
            $message = "Review added successfully!";
        } else {
            $message = "Review updated successfully!";
        }
        return $message;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('recipes::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('recipes::edit');
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
