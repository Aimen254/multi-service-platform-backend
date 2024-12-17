<?php

namespace Modules\Taskers\Http\Controllers\API;

use App\Models\Product;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;

class TaskerReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($request, $limit, $type)
    {
        $id = $request->input('id');
        switch ($type) {
            case 'business':
                $model = Business::findOrFail($id);
                break;
            case 'product':
                $model = Product::findOrFail($id);
                break;
            case 'user':
                $model = User::findOrFail($id);
                break;
            default:
                throw new InvalidArgumentException("Invalid model type: $type");
        }
        $review = $model->reviews();
        if (isset($request->order) && $request->order != 'null') {
            $review->orderBy('rating', $request->order);
        }
        $review = $review->paginate($limit);
        return $review;
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request, $moduleId)
    {
        $type = $request->input('type');
        $modle = null;
        if ($type == 'user') {
            $model = User::findOrFail($request->model_id);
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
}
