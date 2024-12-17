<?php

namespace Modules\Services\Http\Controllers\API;

use App\Models\Review;
use App\Models\Business;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\StandardTag;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Support\Renderable;

class ServiceReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($request, $limit, $type, $moduleId)
    {
        $id = $request->input('id');
        if ($type == 'business') {
            $model = Business::findOrFail($id);
        }
        if ($type == 'product') {
            $model = Product::findOrFail($id);
        }
        $review = $model->reviews()->when($type == 'business', function ($query) use ($moduleId) {
            $query->where('module_id', $moduleId);
        });
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
        return view('services::create');
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
        if ($type == 'business') {
            $model = Business::findOrFail($request->model_id);
        } else if ($type == 'product') {
            $model = Product::findOrFail($request->model_id);
        }

        //Saving data in database
        $review = $model->reviews()->updateOrCreate(['user_id' => $request->user_id], [
            'user_id' => $request->user_id,
            'module_id' => $moduleId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        if ($review->wasRecentlyCreated) {
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
        return view('services::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('services::edit');
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
