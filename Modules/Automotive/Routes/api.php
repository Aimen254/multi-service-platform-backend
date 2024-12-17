<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Automotive\Http\Controllers\API as Automotive;


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

Route::middleware('auth:api')->get('/automotive', function (Request $request) {
    return $request->user();
});

// autenticated routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('/automotive')->group(function () {
        //vehicle contact form 
        Route::apiResource('contact-form', API\ContactFormController::class);
    });

    // Route::prefix('/automotive')->group(function () {
    //     Route::apiResource('contact-form', API\ContactFormController::class)->only('index', 'show');
    // });
});
