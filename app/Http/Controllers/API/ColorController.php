<?php

namespace App\Http\Controllers\API;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Transformers\ColorsTransformer;

class ColorController extends Controller
{
    public function index(Request $request) {
        try {
            $colors = Color::groupBy('title')->whereHas('business', function ($query) {
                $query->whereStatus('active');
            })->where(function ($query) use ($request) { 
                if ($request->input('business')) {
                    $query->where('business_id', $request->input('business'));
                }
            })->get();
            $colors = (new ColorsTransformer)->transformCollection($colors);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $colors,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
             return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
