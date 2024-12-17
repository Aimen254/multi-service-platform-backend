<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Retail\Http\Controllers as Retail;
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

Route::middleware('auth:api')->get('/retail', function (Request $request) {
    return $request->user();
});

// autenticated routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('/retail/{uuid}')->group(function () {
        // bsiness schedule
        Route::apiResource('businessschedules', API\BusinessScheduleController::class);
        Route::post('businessschedule/status/{id}', [
            Retail\API\BusinessScheduleController::class, 'changeStatus'
        ]);

        Route::get('mailings/{businessUuid}', [
            Retail\API\MailingController::class, 'index'
        ]);
        Route::post('mailings', [
            Retail\API\MailingController::class, 'store'
        ]);
        Route::post('mailings/status/{id}', [
            Retail\API\MailingController::class, 'changeStatus'
        ]);
        Route::delete('mailings/destroy/{id}', [
            Retail\API\MailingController::class, 'destroy'
        ]);
        Route::get('mailings/show/{id}', [
            Retail\API\MailingController::class, 'show'
        ]);
        Route::put('mailings/update/{id}', [
            Retail\API\MailingController::class, 'update'
        ]);
       Route::apiResource('businessemails',API\AdditionalEmailController::class);
        // business holidays
        Route::apiResource('/businessholidays', API\BusinessHolidayController::class);
        Route::apiResource('/retailreviews', API\ReviewController::class);
        Route::post('retailreviews/status/{id}', [Retail\API\ReviewController::class, 'changeStatus']);

        // store coupons
        Route::apiResource('coupons', API\CouponController::class);
        Route::post('coupons/change/status/{id}', [Retail\API\CouponController::class, 'changeStatus']);
        
        // stripe connect routes
        Route::apiResource('/stripe', API\StripeController::class);
        Route::post('stripe-payout', [Retail\API\StripeController::class, 'payOut']);
        Route::get('/get-list',[Retail\API\StripeController::class,'getList']);
    });
});
