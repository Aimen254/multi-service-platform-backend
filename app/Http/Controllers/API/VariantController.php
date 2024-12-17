<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Modules\Retail\Entities\ProductVariant;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Transformers\ProductVariantTransformer;

class VariantController extends Controller
{
    public function variantColor(Request $request, $id) {
        try {
            $query = ProductVariant::where('product_id', $id)
                ->where('status', 'active')
                ->where('stock_status', 'in_stock')
                ->where(function ($query) use ($request) {
                    if ($request->input('size')) {
                        $query->where('size_id', $request->size);
                    }
                    if ($request->input('custom_size')) {
                        $query->where('custom_size', $request->custom_size);
                    }
                })
                ->where(function ($query) {
                    $query->where('quantity', '>', 0)
                          ->orWhere('quantity', '=', -1);
                });
    
            // Clone the base query for custom colors
            $standardColorsQuery = clone $query;
            $customColorsQuery = clone $query;
    
            // Get standard colors
            $standardColors = $standardColorsQuery->whereNotNull('color_id')
                ->groupBy('color_id')
                ->get();
    
            // Get custom colors
            $customColors = $customColorsQuery->whereNotNull('custom_color')
                ->groupBy('custom_color')
                ->get();
            $colors = $standardColors->merge($customColors);
            $colors = (new ProductVariantTransformer)->transformCollection($colors);
    
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
    

    public function variantSize(Request $request, $id)
    {
        try {
            $query = ProductVariant::where('product_id', $id)
                ->where('status', 'active')
                ->where('stock_status', 'in_stock')
                ->where(function ($query) use ($request) {
                    if ($request->input('color')) {
                        $query->where('color_id', $request->color);
                    }
                    if ($request->input('custom_color')) {
                        $query->where('custom_color', $request->custom_color);
                    }
                })
                ->where(function ($query) {
                    $query->where('quantity', '>', 0)
                          ->orWhere('quantity', '=', -1);
                });
    
            // Clone the base query for custom sizes
            $standardSizesQuery = clone $query;
            $customSizesQuery = clone $query;
    
            // Get standard sizes
            $standardSizes = $standardSizesQuery->whereNotNull('size_id')
                ->groupBy('size_id')
                ->get();
    
            // Get custom sizes
            $customSizes = $customSizesQuery->whereNotNull('custom_size')
                ->groupBy('custom_size')
                ->get();

            $sizes = $standardSizes->merge($customSizes);
            $sizes = (new ProductVariantTransformer)->transformCollection($sizes);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $sizes,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    


    public function variantPrice(Request $request) {
        try {
            $variant = ProductVariant::where('product_id', $request->product_id)->where('color_id', $request->color)->where('size_id', $request->size)->where('custom_color', $request->custom_color)->where('custom_size', $request->custom_size)->firstOrFail();
            $variant = (new ProductVariantTransformer)->transform($variant);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $variant,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
