<?php

namespace Modules\Classifieds\Http\Controllers\API;

use App\Models\Product;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;

class ClassifiedReviewController extends Controller
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
        } elseif ($type == 'product') {
            $model = Product::findOrFail($id);
        } elseif ($type == 'user') {
            $model = User::findOrFail($id);
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
        return view('classifieds::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request, $moduleId)
    {
        $message = '';
        $type = $request->input('type');
        if ($type == 'business') {
            $moduleId = null;
            $model = Business::findOrFail($request->model_id);
        } else if ($type == 'product') {
            $moduleId = null;
            $model = Product::findOrFail($request->model_id);
        } else if ($type = 'user') {
            $model = User::findOrFail($request->model_id);
        }

        //Saving data in database
        $review = $model->reviews()->updateOrCreate(
            [
                'user_id' => $request->user_id,
            ],
            [
                'user_id' => $request->user_id,
                'module_id' => $moduleId,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );

        if($review->wasRecentlyCreated) {
            $message = "Review added successfully!";
        } else {
            $message = "Review update successfully!";
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
        return view('classifieds::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('classifieds::edit');
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
