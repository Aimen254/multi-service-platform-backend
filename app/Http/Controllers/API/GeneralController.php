<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\TagHierarchy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;

class GeneralController extends Controller
{
    public function newspaperDetails()
    {
        try {
            $newsPaper = User::whereUserType('newspaper')->firstOrFail();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => (new UserTransformer)->transform($newsPaper),
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
