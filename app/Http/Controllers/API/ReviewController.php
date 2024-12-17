<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Models\TagHierarchy;
use App\Models\Business;
use App\Models\Review;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Transformers\ReviewTransformer;
use Modules\Automotive\Entities\ProductAutomotive;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Blogs\Http\Controllers\API\BlogReviewController;
use Modules\Boats\Http\Controllers\API\BoatReviewController;
use Modules\Recipes\Http\Controllers\RecipesReviewController;
use Modules\Retail\Http\Controllers\API\RetailReviewController;
use Modules\Taskers\Http\Controllers\API\TaskerReviewController;
use Modules\Automotive\Http\Controllers\API\VehicleReviewController;
use Modules\Classifieds\Http\Controllers\API\ClassifiedReviewController;
use Modules\Services\Http\Controllers\API\ServiceReviewController;
use Modules\Employment\Http\Controllers\API\EmploymentReviewController;
use Modules\Government\Http\Controllers\API\GovernmentPostReviewController;


class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ReviewRequest $request, $moduleId)
    {
        $limit = $request->input('limit') ? $request->limit : \config()->get('settings.pagination_limit');
        $type = $request->input('type');
        // $module = StandardTag::find($moduleId)->slug;
        $module = StandardTag::where('id', $moduleId)->orWhere('slug', $moduleId)->first();
        $module = $module->slug;
        switch ($module) {
            case 'retail':
                $review = app(RetailReviewController::class)->index($request, $limit, $type);
                break;
            case 'automotive':
                $review = app(VehicleReviewController::class)->index($request, $limit, $type);
                break;
            case 'boats':
                $review = app(BoatReviewController::class)->index($request, $limit, $type);
                break;
            case 'blogs':
                $review = app(BlogReviewController::class)->index($request, $limit, $type);
                break;
            case 'recipes':
                $review = app(RecipesReviewController::class)->index($request, $limit, $type);
                break;
            case 'services':
                $review = app(ServiceReviewController::class)->index($request, $limit, $type, $moduleId);
                break;
            case 'marketplace':
                $review = app(ClassifiedReviewController::class)->index($request, $limit, $type);
                break;
            case 'taskers':
                $review = app(TaskerReviewController::class)->index($request, $limit, $type);
                break;
            case 'employment':
                $review = app(EmploymentReviewController::class)->index($request, $limit, $type);
                break;
            case 'government':
                $review = app(GovernmentPostReviewController::class)->index($request, $limit, $type);
                break;
        }
        if ($type == 'product' || $type == 'vehicle') {
            //Getting data according to product
            // Add it to single product
            $ratings = [
                '5' => $review->count('rating') ? numberFormat($review->where('rating', 5)->count() / $review->count('rating')) : 0,
                '4' => $review->count('rating') ? numberFormat($review->where('rating', 4)->count() / $review->count('rating')) : 0,
                '3' => $review->count('rating') ? numberFormat($review->where('rating', 3)->count() / $review->count('rating')) : 0,
                '2' => $review->count('rating') ? numberFormat($review->where('rating', 2)->count() / $review->count('rating')) : 0,
                '1' => $review->count('rating') ? numberFormat($review->where('rating', 1)->count() / $review->count('rating')) : 0,
                'average' => numberFormat($review->avg('rating')),
            ];
        }
        $paginate = apiPagination($review, $limit);
        $reviews = (new ReviewTransformer)->transformCollection($review);
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'data' => [
                'reviews' => $reviews,
                'ratings' => isset($ratings) ? $ratings : null,
            ],
            'meta' => $paginate,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReviewRequest $request, $moduleId)
    {
        try {
            $reviewMessage = '';
            DB::beginTransaction();
            $module = StandardTag::where('id', $moduleId)->orWhere('slug', $moduleId)->first();
            switch ($module->slug) {
                case 'retail':
                    $reviewMessage = app(RetailReviewController::class)->store($request);
                    break;
                case 'automotive':
                    app(VehicleReviewController::class)->store($request);
                    break;
                case 'boats':
                    $reviewMessage = app(BoatReviewController::class)->store($request);
                    break;
                case 'blogs':
                    $reviewMessage = app(BlogReviewController::class)->store($request, $module->id);
                    break;
                case 'recipes':
                    $reviewMessage = app(RecipesReviewController::class)->store($request, $module->id);
                    break;
                case 'services':
                    $reviewMessage = app(ServiceReviewController::class)->store($request, $module->id);
                    break;
                case 'marketplace':
                    $reviewMessage = app(ClassifiedReviewController::class)->store($request, $module->id);
                    break;
                case 'taskers':
                    $reviewMessage = app(TaskerReviewController::class)->store($request, $module->id);
                    break;
                case 'employment':
                    $reviewMessage = app(EmploymentReviewController::class)->store($request);
                    break;
                case 'government':
                    app(GovernmentPostReviewController::class)->store($request);
                    break;
            }
            DB::commit();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $reviewMessage != '' ? $reviewMessage : 'Review Added successfully.',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($type, $id, Request $request)
    {

        try {
            $limit = $request->input('limit') ? $request->limit : \config()->get('settings.pagination_limit');
            if ($type == 'business') {

                $model = Business::findOrFail($id);
            }
            if ($type == 'product') {
                $model = Product::findOrFail($id);
            }

            if (isset($model) && $model) {
                $review = $model->reviews();
                if (isset($request->order) && $request->order != 'null') {
                    $review->orderBy('rating', $request->order);
                }
                $review = $review->paginate($limit);
            }

            if ($type == 'product') {
                //Getting data according to product
                // Add it to single product
                $ratings = [
                    '5' => $review->count('rating') ? numberFormat($review->where('rating', 5)->count() / $review->count('rating')) : 0,
                    '4' => $review->count('rating') ? numberFormat($review->where('rating', 4)->count() / $review->count('rating')) : 0,
                    '3' => $review->count('rating') ? numberFormat($review->where('rating', 3)->count() / $review->count('rating')) : 0,
                    '2' => $review->count('rating') ? numberFormat($review->where('rating', 2)->count() / $review->count('rating')) : 0,
                    '1' => $review->count('rating') ? numberFormat($review->where('rating', 1)->count() / $review->count('rating')) : 0,
                    'average' => numberFormat($review->avg('rating')),
                ];
            }

            $paginate = apiPagination($review);
            $reviews = (new ReviewTransformer)->transformCollection($review);

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => [
                    'reviews' => $reviews,
                    'ratings' => isset($ratings) ? $ratings : null,
                ],
                'meta' => $paginate,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($moduleId, $id)
    {
        try {
            Review::findOrFail($id)->delete();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Review deleted successfully.',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function getYears()
    {
        $levelOneTag = StandardTag::where('slug', request()->input('module_id'))->first();
        $uniqueYears = Product::active()->with('vehicle')->ModuleBasedProducts($levelOneTag->id)
            ->whereHas('standardTags.levelTwo', function ($query) {
                $query->where('L2', request()->input('make_id'));
            })
            ->whereHas('standardTags.levelThree', function ($query) {
                $query->where('L3', request()->input('model_id'));
            })
            ->get()
            ->pluck('vehicle.year')
            ->unique()
            ->values()
            ->all();

        return $uniqueYears;
    }
}
