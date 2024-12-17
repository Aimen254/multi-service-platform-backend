<?php

namespace Modules\Automotive\Http\Controllers\API;

use App\Models\Review;
use App\Models\Business;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Support\Renderable;
use Modules\Automotive\Entities\VehicleReview;
use Modules\Automotive\Entities\ProductAutomotive;

class VehicleReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($request, $limit, $type)
    {
       
        if ($type == 'business') {
            $business = Business::findOrFail($request->input('model_id'));
            $reviews = $business->reviews()->orderBy('rating', $request->order)->paginate($limit);
            return $reviews;
        }
        if ($type == 'product') {
                $make=request()->input('make_id');
                $model = request()->input('model_id');
                $reviews = Review::whereHas('vehicleReview', function ($query) use ($make, $model) {
                    $query->whereModelId($model)
                        ->whereMakeId($make)
                        ->when(request()->filled('year_min') && request()->filled('year_max'), function ($query) {
                            $query->whereBetween('year', [request()->input('year_min'), request()->input('year_max')]);
                        })
                        ->when(request()->filled('year'), function ($query) {
                            $query->whereYear('year', request()->input('year'));
                        });
                })->with('vehicleReview.maker', 'vehicleReview.model')
                  ->orderBy('created_at', 'desc')->paginate($limit);
            return $reviews;
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('automotive::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store($request)
    {
        if ($request->input('type') == 'business') {
        } else if ($request->input('type') == 'product') {
            $review = Review::updateOrCreate(
                ['id' => $request->input('id')],
                ['module_id' => $request->input('module_id')]
            );
            if ($request->filled('make_id') && $request->filled('model_id')) {
                $vehicleReview = VehicleReview::where('make_id', $request->make_id)
                    ->where('model_id', $request->model_id)->where('year',$request->year)
                    ->first();
                if ($vehicleReview) {
                    $vehicleReview->update([
                        'review_id' => $review->id,
                        'overall_rating' => $this->calculateOverallRating($request),
                        'user_id' => auth()->user()->id,
                        'title'=>request()->input('title'),
                        'reliability'=>request()->input('reliability'),
                        'comfort'=>request()->input('comfort'),
                        'interior_design'=>request()->input('interior_design'),
                        'performance'=>request()->input('performance'),
                        'exterior_styling'=>request()->input('exterior_styling'),
                        'value_for_the_money'=>request()->input('value_for_the_money'),
                        'year'=>request()->input('year'),
                        
                    ]);
                } else {
                    $review->vehicleReview()->create(array_merge($request->all(), [
                        'overall_rating' => $this->calculateOverallRating($request),
                        'user_id' => auth()->user()->id
                    ]));
                }
            }
        }
    }


    private function calculateOverallRating(Request $request): float
    {
        $ratingFields = ['comfort', 'interior_design', 'performance', 'value_for_the_money', 'exterior_styling', 'reliability'];
        $totalRating = 0;

        foreach ($ratingFields as $field) {
            $totalRating += (float)$request->input($field, 0);
        }

        return $totalRating / count($ratingFields);
    }





    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('automotive::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('automotive::edit');
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
