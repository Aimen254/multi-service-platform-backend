<?php

use App\Http\Controllers\API as API;
use App\Http\Controllers\API\FollowProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use Modules\Retail\Http\Controllers\API\CouponsController;
use Modules\Retail\Http\Controllers\API\ProductCouponController;
use Modules\Retail\Http\Controllers\API\ProductDiscountController;
use Modules\Retail\Http\Controllers\API\ProductTaxController;
use Modules\Retail\Http\Controllers\API\VariantsController;
use Modules\Services\Http\Controllers\API as ServiceAPI;
use App\Http\Controllers\API\PublicProfileController;
use App\Http\Controllers\API\InappropriateProductController;
use App\Http\Controllers\API\UserStandardTagsSettingController;

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

// authentication api routes
Route::post('signin', [API\Auth\AuthenticationController::class, 'login']);
Route::post('signup', [API\Auth\AuthenticationController::class, 'register']);
Route::post('forgot-password', [API\Auth\AuthenticationController::class, 'forgotPassword']);
Route::post('reset-password', [API\UserProfileController::class, 'changePassword']);
Route::post('verify-otp',  [API\Auth\AuthenticationController::class, 'verifyOtp']);
Route::post('resend-email', [API\Auth\AuthenticationController::class, 'resendEmail']);
Route::post('account-verify', [API\Auth\AuthenticationController::class, 'accountVerification']);

// Newspaper Details
Route::get('/newspaper', [API\GeneralController::class, 'newspaperDetails']);

// autenticated routes
Route::middleware(['auth:sanctum'])->group(function () {
    //
    Route::post('/logout', [API\Auth\AuthenticationController::class, 'logout']);
    // myLv page API's
    Route::get('my_lv/categories', [API\MyLvController::class, 'getCategories']);
    Route::get('my/lv/products', [API\MyLvController::class, 'getProducts']);
    // Route::middleware(['verified'])->group(function () {
        // user profile routes
        Route::get('/view-profile', [API\UserProfileController::class, 'viewProfile']);
        Route::get('/user-profile', [API\UserProfileController::class, 'getUserProfile']);
        Route::post('/profile-settings', [API\UserProfileController::class, 'settings']);
        Route::post('/address-settings',  [API\UserProfileController::class, 'updateAddress']);
        Route::delete('/remove-address/{id}',  [API\UserProfileController::class, 'removeAddress']);
        Route::post('/change-password', [API\UserProfileController::class, 'changePassword']);
        Route::post('/payment-settings', [API\UserProfileController::class, 'paymentSettings']);
        Route::get('/addresses', [API\UserProfileController::class, 'getAllAddresses']);
        Route::get('/edit-address/{id}', [API\UserProfileController::class, 'editAddress']);
        Route::patch('/addresses/update-status/{id}', [API\UserProfileController::class, 'updateAddressStatus']);
        Route::apiResource('recepients', API\RecepientController::class);

        // module based public profile routes
        Route::controller(PublicProfileController::class)
            ->prefix('public-profile')
            ->as('public.profile.')
            ->group(function () {
                Route::get('/{moduleId}', 'index');
                Route::post('/update-or-store', 'updateOrStore');
                Route::post('/image', 'updateOrStoreImage');
                Route::get('/edit/{id}', 'show');
                Route::delete('delete/{id}', 'destroy');
            });

        // follow profile routes
        Route::controller(FollowProfileController::class)->prefix('profiles')
            ->group(function () {
                Route::post('/follow', 'followProfile');
                Route::get('follow/{type}', 'index');
                Route::post('change-status/{id}/{status}', 'changeRequestStatus');
                Route::delete('cancel/request/{follower_id}/{following_id}', 'cancelRequest');
            });

        // order route
        Route::get('/orders',  [API\OrderController::class, 'index']);
        Route::get('/view-order/{uuid}', [API\OrderController::class, 'viewOrder']);
        Route::post('/place-order', [API\OrderController::class, 'placeOrder']);
        Route::post('cancel-order', [API\OrderController::class, 'cancelOrder']);
        Route::post('item-refund', [API\OrderController::class, 'itemRefund']);
        Route::post('order-status/{businessUuid}/{id}', [API\OrderController::class, 'updateOrderStatus']);
        Route::post('/calculate/delivery-fee', [API\OrderController::class, 'calculateDeliveryFee']);

        // Cart route
        Route::apiResource('carts', API\CartController::class);
        Route::post('coupon-verification', [API\CartController::class, 'verifyCoupon']);
        Route::get('get-user-cart', [API\CartController::class, 'getUserCart']);

        //Write Business Review
        Route::group(['prefix' => 'business'], function () {
            Route::apiResource('reviews', API\ReviewController::class)->only(['store']);
        });

        //Wishlist Routes
        Route::apiResource('wishlist', API\WishlistController::class);

        //Like Routes
        Route::apiResource('like', API\LikeController::class);

        // Reposts
        Route::apiResource('repost', Api\RepostController::class);

        //Credit Card
        Route::apiResource('credit-card', API\CreditCardController::class);

        //Retrieve Patment Method
        Route::post('payment-method', [API\CreditCardController::class, 'retrievePaymentMethod']);

        // delete customer account
        Route::post('/delete/account', [API\UserProfileController::class, 'deleteAccount']);

        // get search history
        Route::get('search/history/{module}', [API\SearchController::class, 'getSearchHistory']);
        Route::delete('remove/search/history/{id}', [API\SearchController::class, 'removeSearchHistory']);
    // });
    Route::prefix('{module_id}')->group(function () {
        //garage api's
        Route::apiResource('businesses', API\BusinessController::class)->only('store', 'update', 'destroy');
        Route::get('business/change/status/{id}', [API\BusinessController::class, 'changeStatus']);
        Route::apiResource('/reviews', API\ReviewController::class)->only('store', 'destroy');
        Route::apiResource('/subscription', API\SubuscriptionController::class);
        Route::post('/getPlan', [API\SubuscriptionController::class, 'getPlan']);
        Route::post('/pay-last-invoice', [API\SubuscriptionController::class, 'payLastInvoice']);
    });

    Route::prefix('{moduleId}')->group(function () {
        //product private routes
        Route::apiResource('products', API\ProductController::class)->only('store', 'update', 'destroy');
        Route::patch('/product/change-status/{uuid}', [API\ProductController::class, 'updateStatus']);
        Route::get('likes-users/{uuid}', [API\ProductController::class, 'likeUsers']);

        Route::apiResource('dream-products', API\DreamProductController::class);
        Route::post('/sync-category-products/{id}', [API\DreamProductController::class, 'categoryProduct']);
        Route::get('/attributes', [API\AttributeController::class, 'attributes']);
        Route::get('/attributes/{uuid}', [API\AttributeController::class, 'index']);
        Route::post('/attribute-tags/assign/{uuid}', [API\AttributeController::class, 'assignTags']);
        Route::get('/attribute_tags/search/{attribute_id}', [API\AttributeController::class, 'searchTags']);
    });

    // store, update, delete comments
    Route::apiResource('comments', API\CommentController::class)->only('store', 'update', 'destroy');
    Route::apiResource('inappropriate-products', InappropriateProductController::class)->only(['store', 'destroy']);

    Route::prefix('/{module?}')->group(function () {
        // contact form
        Route::apiResource('contact-form', API\ContactFormController::class);

        // calendar events
        Route::apiResource('calendar-events', API\CalendarEventController::class);
        // calendar event status
        Route::post('calendar-events/change-status/{id}', [API\CalendarEventController::class, 'changeStatus']);
    });

    // get users
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/', 'index');
        Route::post('/store', 'store');
        Route::get('/{id}/edit', 'edit');
        Route::put('/update/{id}', 'update');
        Route::patch('/{id}/status-update', 'updateStatus');
        Route::delete('/{id}/destroy', 'destroy');
        Route::get('/products/{userId}', 'getProductsAgainstUser');
    });

    Route::controller(UserStandardTagsSettingController::class)->prefix('{module_id}')->group(function () {
        Route::get('/user-tags/{userId}', 'userStandardTags');
        Route::get('/user-level-three-tags', 'getLevelThreeTags');
        Route::get('/remove-product-tags', 'removeProductTags');
        Route::post('/assign/standard-tags/{userId}', 'assignTags');
    });


    // New API's List
    Route::prefix('{module_id?}')->group(function () {
        Route::apiResource('products', API\ProductController::class)->only('index', 'show');
        Route::get('headlines', [API\ProductController::class, 'getHeadlines']);
        Route::apiResource('businesses', API\BusinessController::class)->only('index', 'show');
        Route::apiResource('productTags', API\StandardTagsController::class)->only('index', 'show');
        Route::get('business/{uuid}/product-tags', [API\StandardTagsController::class, 'getProductTag']);
        Route::get('filters/{tagId?}', [API\StandardTagsController::class, 'getFiltersWithTag']);
        Route::get('industry-tags', [API\StandardTagsController::class, 'getIndustryTags']);
        Route::get('verify-hierarchy/', [API\StandardTagsController::class, 'verifyHierarchy']);
        Route::apiResource('/reviews', API\ReviewController::class)->only('index', 'destroy');
        Route::get('/reviews/unique-years', [API\ReviewController::class, 'getYears']);
        Route::post('products/main-image/{uuid}/{id?}', [API\ProductController::class, 'updateMainImage']);
        Route::post('product-media/{uuid}', [API\ProductController::class, 'productMedia']);
        Route::delete('product-media-delete/{uuid}/{id}', [API\ProductController::class, 'destroyMedia']);
    });

    // add banners
    Route::get('ads', [API\SettingsController::class, 'salesBanner']);

    // module tags
    Route::get('/module-tags', [API\StandardTagsController::class, 'getModuleTags']);
    Route::group(['prefix' => 'level'], function () {
        Route::get('/one', [API\TagController::class, 'levelOneTags']);
        Route::get('/two/{levelOne}', [API\TagController::class, 'levelTwoTags']);
        Route::get('/three/{levelOne}/{levelTwo?}', [API\TagController::class, 'levelThreeTags']);
        Route::get('/four/{levelOne}/{levelTwo}/{levelThree}', [
            API\TagController::class, 'levelFourTags'
        ]);
    });

    Route::get('/tags/{uuid?}', [API\TagController::class, 'getProductTags']);

    Route::prefix('{module_id?}')->group(function () {
        Route::post('tags/{uuid?}/assign/', [API\TagController::class, 'assignTags']);
    });

    // search controllers
    Route::post('/store/search', [API\SearchController::class, 'storeSearch']);

    // commetns listing
    Route::apiResource('comments', API\CommentController::class)->only('index');

    // related items
    Route::post('related-items', [API\ProductController::class, 'relatedItems']);

    // variants
    Route::get('variant-colors/{id}', [API\VariantController::class, 'variantColor']);
    Route::get('variant-sizes/{id}', [API\VariantController::class, 'variantSize']);
    Route::get('variant-price', [API\VariantController::class, 'variantPrice']);

    Route::prefix('{module}')->group(function () {
        Route::apiResource('products/{uuid}/variants', VariantsController::class)->only('index','store','destroy');
        Route::put('variants/{id}/status', [VariantsController::class, 'updateStatus']);
        Route::put('product-variants/{uuid}/update/{id}', [VariantsController::class, 'update']);

        Route::apiResource('products/{uuid}/coupons', ProductCouponController::class)->only('index','store','destroy');
        Route::put('/products/{uuid}/coupons/{id}/status', [ProductCouponController::class, 'changeStatus']);

        Route::apiResource('products/{uuid}/discount', ProductDiscountController::class)->only('index','store');

        Route::apiResource('products/{uuid}/tax', ProductTaxController::class)->only('index','store');
    });

    // business search suggestions
    Route::get('search-business', [API\BusinessController::class, 'businessSearchSuggestion']);

    // colors
    Route::get('colors', [API\ColorController::class, 'index']);

    // sizes
    Route::get('sizes', [API\SizeController::class, 'index']);

    //General Settings
    Route::apiResource('settings', API\SettingsController::class);

    // conversation
    Route::prefix('{module?}')->group(function () {
        Route::apiResource('conversations', API\ConversationController::class);

        // business chat permission
        Route::get('business-chat/{conversation_id}', [API\BusinessController::class, 'businessChatPermission']);
    });
});

// verify auth token
Route::get('verfiy-token', [API\ConversationController::class, 'VerifyToken']);

Route::post('/search', API\SearchController::class);
