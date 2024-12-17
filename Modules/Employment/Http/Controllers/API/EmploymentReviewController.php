<?php

namespace Modules\Employment\Http\Controllers\API;

use App\Models\Product;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;

class EmploymentReviewController extends Controller
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
        return view('services::create');
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
        $review = $model->reviews()->updateOrCreate(['id' => $request->input('id')], [
            'user_id' => $request->user_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        if($review->wasRecentlyCreated) {
            $message = "Review added successfully!";
        } else {
            $message = "Review update successfully!";
        }

        return $message;
    }
}
