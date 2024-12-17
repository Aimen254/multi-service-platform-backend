<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\MyCategoryRepository;
use App\Transformers\MyCategoryTransformer;
use App\Transformers\MyLv\ProductTransformer;

class MyLvController extends Controller
{
    protected $myCategoryRepository;
    protected $limit = 4;

    public function __construct(MyCategoryRepository $myCategoryRepository)
    {
        $this->myCategoryRepository = $myCategoryRepository;
        $this->limit = request()->input('limit') ? request()->limit : $this->limit;
    }

    /**
     * Provision a new web server.
     */
    public function getCategories(Request $request)
    {
        $myCategories = $this->myCategoryRepository->activeCategoriesList();

        $paginate = apiPagination($myCategories, $this->limit);
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'data' => (new MyCategoryTransformer)->transformCollection($myCategories->values()),
            'meta' => $paginate,
        ], JsonResponse::HTTP_OK);
    }

    public function getProducts()
    {
        try {
            $products = $this->myCategoryRepository->productsList();
            $paginate = apiPagination($products, $this->limit);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => (new ProductTransformer)->transformCollection($products),
                'meta' => $paginate
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
