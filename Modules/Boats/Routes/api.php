<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Boats\Http\Controllers\API as Boat;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/boats', function (Request $request) {
    return $request->user();
});

// autenticated routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('/boats')->group(function () {
        //boat contact form 
        Route::apiResource('contact-form', API\ContactFormController::class);
        // Route::apiResource('dream-cars', API\DreamBoatController::class);
    });
});

// unauthenticated routes
// Route::prefix('/boats')->group(function () {
//     Route::apiResource('contact-form', API\ContactFormController::class)->only('index', 'show');
// });
