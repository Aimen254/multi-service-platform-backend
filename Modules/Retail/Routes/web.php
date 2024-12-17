<?php

use Inertia\Inertia;
use Dashboard\SizeController;
use Dashboard\ColorController;
use Dashboard\OrderController;
use Dashboard\ProductController;
use Dashboard\BusinessController;
use Dashboard\ProductMediaController;
use Dashboard\Products\TaxController;
use Illuminate\Support\Facades\Route;
use Dashboard\Products\VariantController;
use Dashboard\Products\DiscountController;
use Dashboard\Products\ProductTagsController;
use Modules\Retail\Http\Controllers as Retail;
use Dashboard\Heirarchies\HierarchiesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('retail')->as('retail.')->group(function () {
    Route::get('/dashboard/{type?}', [Retail\PanelController::class, 'index'])
        ->middleware(['auth', 'verified', 'check_subscription'])->name('dashboard.panel');

    Route::middleware(['auth', 'ValidateRoleStatus'])
        ->as('dashboard.')
        ->prefix('dashboard/{moduleId?}')
        ->group(function () {
            /*
            * Business
            */
            Route::group(['middleware' => ['can:view_business']], function () {
                Route::resource('businesses', Dashboard\BusinessController::class);
                Route::get('business/change/status/{id}', [Retail\Dashboard\BusinessController::class, 'changeStatus'])->name('business.status');
            });

            /*
            * Business Settings
            */
            Route::group(['prefix' => 'business/{business_uuid}', 'as' => 'business.'], function () {
                // general settings
                Route::group(['middleware' => ['can:view_business_settings']], function () {
                    Route::resource('/settings', Dashboard\SettingsController::class);
                });
                // stripe connect
                Route::group(['middleware' => ['can:view_business_settings']], function () {
                    Route::get('/stripe', [Retail\Dashboard\StripeController::class, 'index'])->name('stripe.index');
                    Route::get('stripe-connect', [Retail\Dashboard\StripeController::class, 'redirectToStripe'])->name('redirect.stripe');
                    Route::get('connect/{token}', [Retail\Dashboard\StripeController::class, 'saveStripeAccount'])->name('save.stripe');
                    Route::post('save-stripe-bank-account', [Retail\Dashboard\StripeController::class, 'saveStripeBankAccount'])->name('stripe.savebank');
                    Route::post('stripe-payout', [Retail\Dashboard\StripeController::class, 'payout'])->name('payout.stripe');
                    Route::post('get-list', [Retail\Dashboard\StripeController::class, 'getList'])->name('stripe.list');
                });
                //Business scheduling
                Route::group(['middleware' => ['can:view_business_schedule_time']], function () {
                    Route::resource('/businessschedules', Dashboard\BusinessScheduleController::class);
                    Route::post('businessschedule/status/{id}/{type}', [
                        Retail\Dashboard\BusinessScheduleController::class, 'changeStatus'
                    ])->name('businessschedule.status');
                    Route::get('businessschedules/schedule/{scheduleid}', [
                        Retail\Dashboard\BusinessScheduleController::class, 'getSchedule'
                    ])->name('businessschedule.schedule');
                    Route::resource('/businessholidays', Dashboard\BusinessHolidayController::class);
                });
                // Business Additional Emails
                Route::group(['middleware' => ['can:view_additional_emails']], function () {
                    Route::resource('/emails', Dashboard\AdditionalEmailController::class);
                });
                // Mailing settings
                Route::group(['middleware' => ['can:view_business_mailings']], function () {
                    Route::resource('/mailings', Dashboard\MailingController::class);
                    Route::get('mailing/change/status/{id}', [Retail\Dashboard\MailingController::class, 'changeStatus'])->name('mailing.status');
                });
                // Business Extra Tags
                Route::group(['middleware' => ['can:view_extra_tags']], function () {
                    Route::resource('/business-tags', Dashboard\Business\ExtraTagsController::class);
                    Route::post('/business-tags/clone/{id}', [
                        Retail\Dashboard\Business\ExtraTagsController::class, 'cloneTag'
                    ])->name('business-tags.clone-tag');
                });
                // business admin settings
                Route::group(['middleware' => ['can:view_admin_settings']], function () {
                    Route::get('/admin-setting',  [Retail\Dashboard\SettingsController::class, 'standardTagsAdminSettings'])->name('admin-settings');
                    Route::get('/chat-setting',  [Retail\Dashboard\SettingsController::class, 'chatAdminSettings'])->name('chat-settings');
                    Route::post('enable/business-chat', [Retail\Dashboard\SettingsController::class, 'enableDisableChat'])
                    ->name('enable.business-chat');
                    Route::post('assign/standard-tags', [Retail\Dashboard\SettingsController::class, 'assignTags'])
                        ->name('assign.standard-tags');
                    Route::get('admin/settings', [Retail\Dashboard\SettingsController::class, 'adminSettings'])->name('admin.settings');
                    Route::post('settings/update', [Retail\Dashboard\SettingsController::class, 'updateSettings'])
                        ->name('platform-fee-type.update');
                    Route::get('admin/assign-fee-type', [Retail\Dashboard\SettingsController::class, 'assignFeeType'])->name('admin.assignFeeType');
                    Route::post('assign-fee-type/update', [Retail\Dashboard\SettingsController::class, 'updateFeeType'])
                        ->name('feeType.update');
                    Route::post('/remove-product-tags',  [Retail\Dashboard\SettingsController::class, 'removeProductTags'])->name('remove-product-tags');
                    Route::get('business-level-three-tags', [Retail\Dashboard\SettingsController::class, 'getLevelThreeTags'])->name('business-level-three-tags');
                });
                // Delivery Zone
                Route::group(['middleware' => ['can:view_delivery_zones']], function () {
                    Route::resource('/deliveryzones', Dashboard\Business\DeliveryZoneController::class);
                });
                // Business Reviews
                Route::group(['middleware' => ['can:view_reviews']], function () {
                    Route::resource('/reviews', Dashboard\Business\ReviewController::class);
                    Route::get('review/status/{id}', [Retail\Dashboard\Business\ReviewController::class, 'changeStatus'])->name('review.status');
                });
            });

            //subscription plans
            Route::group(['prefix' => 'subscription', 'as' => 'subscription.'], function () {
                Route::resource('/plan', Dashboard\Subscription\PlanController::class)->middleware('can:view_subscription_plan');
            });

            // orders routes
            Route::group(['prefix' => 'business/{business_uuid}', 'as' => 'business.'], function () {
                // Business Orders
                Route::group(['prefix' => 'order/type/{type}', 'as' => 'order.type.'], function () {
                    Route::group(['middleware' => ['can:view_orders']], function () {
                        Route::resource('/orders', OrderController::class);
                        Route::post('item-refund', [Retail\Dashboard\OrderController::class, 'itemRefund'])->name('order.item.refund');
                    });
                });

                // business coupons
                Route::group(['middleware' => ['can:view_business_coupons']], function () {
                    Route::resource('/coupons', Dashboard\CouponController::class);
                });
                Route::get('coupons/change/status/{id}', [
                    Retail\Dashboard\CouponController::class, 'changeStatus'
                ])->name('coupon.change.status');
            });

            // products section routes
            Route::group(['prefix' => 'business/{business_uuid}', 'as' => 'business.'], function () {
                Route::group(['middleware' => ['can:view_products']], function () {
                    Route::resource('products', ProductController::class);
                    Route::get(
                        'product/status/{id}',
                        [Retail\Dashboard\ProductController::class, 'changeStatus']
                    )->name('product.status');

                    Route::get(
                        'tags/{tagId}/{level}',
                        [Retail\Dashboard\ProductController::class, 'getTags']
                    )->name('product.tags');
                    //products images
                });

                // variant size
                Route::group(['middleware' => ['can:view_product_sizes']], function () {
                    Route::resource('/sizes', SizeController::class);
                });

                // // variant color
                Route::group(['middleware' => ['can:view_product_colors']], function () {
                    Route::resource('/colors', ColorController::class);
                });
            });
            Route::group(['prefix' => 'product/{uuid}', 'as' => 'product.'], function () {
                //products images
                Route::group(['middleware' => ['can:view_product_images']], function () {
                    Route::resource('media', ProductMediaController::class);
                });
                // discounts or product coupons section
                Route::resource('coupons', DiscountController::class);
                Route::get('discount/status/{id}', [Retail\Dashboard\Products\DiscountController::class, 'changeStatus'])->name('discount.status');
                Route::get('discount', [Retail\Dashboard\Products\DiscountController::class, 'productDiscount'])->name('discount');
                Route::put('discount/update', [Retail\Dashboard\Products\DiscountController::class, 'discountUpdate'])->name('discount.update');

                // product attributes tags section
                Route::get('attribute-tags/', [Retail\Dashboard\Products\AttributeTagsController::class, 'index'])->name('attribute-tags.index');
                Route::get('attribute-tags/assign/', [Retail\Dashboard\Products\AttributeTagsController::class, 'assignTags'])->name('attribute-tags.assign');
                Route::get('attribute-tags/search/', [Retail\Dashboard\Products\AttributeTagsController::class, 'searchTags'])->name('search.attribute.tags');

                // product tags routes
                Route::resource('product-tags', ProductTagsController::class)
                    ->only(['index']);
                Route::get('product-tags/assign', [Retail\Dashboard\Products\ProductTagsController::class, 'assignTags'])->name('product-tags.assign');

                // product Tax
                Route::resource('taxes', TaxController::class);

                // variants
                Route::resource('variants', VariantController::class);
                Route::get('variant/status/{id}', [Retail\Dashboard\Products\VariantController::class, 'changeStatus'])->name('variant.status');
                Route::post('variant/quantity/{id}', [Retail\Dashboard\Products\VariantController::class, 'updateQuantity'])->name('variant.updateQuantity');
                Route::post('variant/auto/generate', [Retail\Dashboard\Products\VariantController::class, 'autoGenerateVariants'])->name('variant.auto.generate');
                Route::delete('variant/delete/{ids}', [Retail\Dashboard\Products\VariantController::class, 'deleteAll'])->name('variant.deleteAll');
                Route::post('variant/imageUpload', [Retail\Dashboard\Products\VariantController::class, 'bulkImageUpload'])->name('variant.imageUpload');
            });


            /*
            * retail tag hierarchies
            */
            Route::group(['middleware' => ['can:view_tag_hierarchies']], function () {
                Route::resource('level/{level}/tag-hierarchies', HierarchiesController::class)->only(['index', 'store', 'show']);
                Route::get('level-tags/{levelTwo}/{levelThree?}', [Retail\Dashboard\Heirarchies\HierarchiesController::class, 'getTagWithLevel'])->name('tag-hierarchies.getTagWithLevel');
                Route::get('search/standardTags', [Retail\Dashboard\Heirarchies\HierarchiesController::class, 'searchStandardTags'])->name('search.standardTags');
            });
        });

    Route::get('/seed', [Retail\Dashboard\OrderController::class, 'seedCustomers']);
});
