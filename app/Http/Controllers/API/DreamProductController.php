<?php

namespace App\Http\Controllers\API;

use App\Models\StandardTag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Transformers\DreamCarTransformer;
use Modules\Automotive\Entities\DreamCar;
use App\Http\Requests\DreamProductRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Jobs\SyncMyCategoryProduct;

class DreamProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($moduleId)
    {

        try {

            $limit = request()->input('limit')
                ? request()->limit : \config()->get('settings.pagination_limit');
            $module = StandardTag::where('slug', $moduleId)->orWhere('id', $moduleId)->first();
            $options = [
                'module' => in_array($module->slug, ['posts', 'news', 'obituaries', 'recipes', 'blogs', 'marketplace', 'services', 'employment', 'taskers', 'notices','retail', 'real-estate', 'events']) ? $module->slug : null
            ];
            $dreamCar = request()->user()->dreamCars()->where('module_id', $module->id)->with(['maker', 'model', 'beds', 'baths', 'squareFeets'])->latest()->paginate($limit);
            $paginate = apiPagination($dreamCar, $limit);
            $dreamCars = (new DreamCarTransformer)->transformCollection($dreamCar, $options);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $dreamCars,
                'meta' => $paginate,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DreamProductRequest $request)
    {
        try {
            $user = request()->user();
            $message = '';
            $module = StandardTag::where('id', $request->module)->orWhere('slug', $request->module)->first();
            if ($module) {
                $request->merge([
                    'module_id' => $module->id
                ]);
            }
            $car = null;

            switch ($module->slug) {
                case 'obituaries':
                    $message = 'Loved one ' . $module->slug . ' category added successfully';
                    break;
                case 'recipes':
                case 'news':
                case 'posts':
                case 'blogs':
                case 'services':
                case 'marketplace':
                case 'taskers':
                case 'government':
                case 'employment':
                case 'automotive':
                case 'notices':
                case 'real-estate':
                case 'events':
                    $message = 'Added to favorite ' . formatString($module->slug) . ' category';
                    break;
                default:
                    $message = 'Dream ' . $module->slug . ' added successfully';
            }

            if (!$user->dreamCars()->where('make_id', $request->make_id)
                ->where('module_id', $request->module_id)
                ->where('model_id', $request->model_id)
                ->when(request()->input('level_four_tag_id'), function ($query) {
                    $query->where(
                        'level_four_tag_id',
                        request()->input('level_four_tag_id')
                    );
                })
                ->when(in_array($module->slug, ['boats', 'automotive']), function ($query) use ($request) {
                    $query->where('from', $request->from)->where('to', $request->to);
                })
                ->when(in_array($module->slug, ['real-estate']), function ($query) use ($request) {
                    $query->where('min_price', $request->min_price)
                        ->where('max_price', $request->max_price)
                        ->where('bed', $request->bed)->where('bath', $request->bath)
                        ->where('square_feet', $request->square_feet);
                })
                ->exists()) {
                $car = $user->dreamCars()->create($request->all());
            } else {
                switch ($module->slug) {
                    case 'obituaries':
                        $message = 'Loved one ' . $module->slug . ' category updated successfully';
                        break;
                    case 'news':
                    case 'posts':
                    case 'recipes':
                    case 'blogs':
                    case 'services':
                    case 'marketplace':
                    case 'taskers':
                    case 'government':
                    case 'automotive':
                    case 'notices':
                    case 'real-estate':
                    case 'events':
                        $message = 'Favorite ' . formatString($module->slug) . ' category updated successfully';
                        break;
                    default:
                        $message = 'Dream ' . $module->slug . ' updated successfully';
                }
            }
            $options = [
                'module' => in_array($module->slug, ['posts', 'news', 'obituaries', 'recipes', 'blogs', 'marketplace', 'services', 'taskers', 'notices', 'real-estate', 'events']) ? $module->slug : null
            ];
            $dreamCar = $car ? (new DreamCarTransformer)->transform($car, $options) : null;
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $message,
                'data' => $dreamCar,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
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
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DreamProductRequest $request, $moduleId, $id)
    {
        try {
            $car = DreamCar::findOrFail($id);
            $message = '';
            $module = StandardTag::where('slug', $moduleId)->orWhere('id', $moduleId)->first();

            $user = request()->user();
            if ($user->dreamCars()->where('id', '<>', $car?->id)->where('make_id', $request->make_id)
                ->where('module_id', $module->id)
                ->where('model_id', $request->model_id)
                ->when(request()->input('level_four_tag_id'), function ($query) {
                    $query->where(
                        'level_four_tag_id',
                        request()->input('level_four_tag_id')
                    );
                })
                ->when(in_array($module->slug, ['boats', 'automotive']), function ($query) use ($request) {
                    $query->where('from', $request->from)->where('to', $request->to);
                })
                ->when(in_array($module->slug, ['real-estate']), function ($query) use ($request) {
                    $query->where('min_price', $request->min_price)
                        ->where('max_price', $request->max_price)
                        ->where('bed', $request->bed)->where('bath', $request->bath)
                        ->where('square_feet', $request->square_feet);
                })
                ->exists()
            ) {
                return response()->json([
                    'status' => JsonResponse::HTTP_CONFLICT,
                    'message' => 'Already exists.',
                ], JsonResponse::HTTP_CONFLICT);
            } else {
                $car->update($request->all());
            }

            $options = [
                'module' => in_array($module->slug, ['posts', 'news', 'obituaries', 'recipes', 'blogs', 'marketplace', 'services', 'taskers', 'government', 'notices', 'real-estate', 'events']) ? $module->slug : null
            ];
            $dreamCar = (new DreamCarTransformer)->transform($car, $options);

            switch ($module->slug) {
                case 'obituaries':
                    $message = 'Loved one ' . $module->slug . ' category updated successfully';
                    break;
                case 'news':
                case 'posts':
                case 'recipes':
                case 'blogs':
                case 'services':
                case 'marketplace':
                case 'government':
                case 'taskers':
                case 'automotive':
                case 'notices':
                case 'real-estate':
                case 'events':
                    $message = 'Favorite ' . formatString($module->slug) . ' category updated successfully';
                    break;
                default:
                    $message = 'Dream ' . $module->slug . ' updated successfully';
            }

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $message,
                'data' => $dreamCar,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Data not found.'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
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
            $module = StandardTag::where('slug', $moduleId)->orWhere('id', $moduleId)->first();
            $dreamCar = DreamCar::findOrFail($id);
            $dreamCar->delete();
            switch ($module->slug) {
                case 'obituaries':
                    $message = 'Loved one ' . $module->slug . ' category deleted successfully';
                    break;
                case 'news':
                case 'posts':
                case 'recipes':
                case 'blogs':
                case 'services':
                case 'marketplace':
                case 'government':
                case 'taskers':
                case 'automotive':
                case 'notices':
                case 'real-estate':
                case 'events':
                    $message = 'Favorite ' . formatString($module->slug) . ' category deleted successfully';
                    break;
                default:
                    $message = 'Dream ' . $module->slug . ' deleted successfully';
            }

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $message,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Data not found.'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function categoryProduct($module, $id) 
    {
        try {
            $category = DreamCar::findOrFail($id);
            SyncMyCategoryProducts::dispatch($category, $module);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Products synced with category',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Category not found.'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
