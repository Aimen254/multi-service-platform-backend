<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class FavoriteCategoryController extends Controller
{
    public function getCategories(Request $request)
    {
        try {
            $limit = request()->input('limit')
                ? request()->limit : \config()->get('settings.pagination_limit');
            $mycategories = request()->user()->dreamCars()->latest()->get()->reject(function($record) => {});
            return $mycategories;
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
