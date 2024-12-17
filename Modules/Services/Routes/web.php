<?php

use Illuminate\Support\Facades\Route;
use Modules\Services\Http\Controllers\Dashboard as Services;

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

// Route::prefix('services')->group(function() {
//     Route::get('/', 'ServicesController@index');
// });

Route::prefix('services')
    ->as('services.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::prefix('dashboard')
            ->as('dashboard.')
            ->group(function () {
                Route::prefix('/{moduleId}')
                    ->group(function () {
                        //subscription plans
                        Route::group(['prefix' => 'subscription', 'as' => 'subscription.'], function () {
                            Route::resource('/plan', Dashboard\Subscription\PlanController::class)->middleware('can:view_subscription_plan');
                        });

                        Route::group(['middleware' => ['can:view_tag_hierarchies']], function () {
                            Route::resource('level/{level}/tag-hierarchies', Dashboard\HierarchiesController::class)->only(['index', 'store', 'show']);
                            Route::get('level-tags/{levelTwo}/{levelThree?}', [Services\HierarchiesController::class, 'getTagWithLevel'])->name('tag-hierarchies.getTagWithLevel');
                            Route::get('search/standardTags', [Services\HierarchiesController::class, 'searchStandardTags'])->name('search.standardTags');
                        });
                        // businesses
                        Route::group(['middleware' => ['can:view_business']], function () {
                            Route::resource('service-provider', Dashboard\BusinessController::class);
                            Route::get('service-provider/change/status/{id}', [Services\BusinessController::class, 'changeStatus'])->name('service-provider.status');
                        });

                        Route::prefix('services/{uuid}')->as('services.')->group(function () {
                            //vehicles images
                            Route::group(['middleware' => ['can:view_product_images']], function () {
                                Route::resource('media', Dashboard\Service\ServiceMediaController::class);
                            });
                            // product tags routes
                            Route::resource('service-tags', Dashboard\Service\ServiceTagsController::class)->only(['index']);
                            Route::resource('reviews', Dashboard\Service\ServiceReviewController::class);
                            Route::get('service-tags/assign', [Services\Service\ServiceTagsController::class, 'assignTags'])->name('service-tags.assign');

                            // comments
                            Route::resource('comments', Dashboard\Service\CommentController::class);
                        });

                        Route::group(['prefix' => 'service-provider/{business_uuid?}', 'as' => 'service-provider.'], function () {

                            Route::group(['middleware' => ['can:view_contact_form']], function () {
                                Route::resource('/communication-portal', Dashboard\Service\ContactFormController::class);
                            });

                            Route::resource('services', Dashboard\Service\ServiceController::class);
                            Route::get('tags/{tagId}/{level}', [Services\Service\ServiceController::class, 'getTags'])->name('services.tags');
                            Route::get('services/status/{id}', [Services\Service\ServiceController::class, 'changeStatus'])->name('services.status');

                            // Business Reviews
                            Route::group(['middleware' => ['can:view_reviews']], function () {
                                Route::resource('/reviews', Dashboard\ReviewController::class);
                                Route::get('review/status/{id}', [Services\ReviewController::class, 'changeStatus'])->name('review.status');
                            });

                            //Business scheduling
                            Route::group(['middleware' => ['can:view_business_schedule_time']], function () {
                                Route::resource('/businessschedules', Dashboard\BusinessScheduleController::class);
                                Route::post('businessschedule/status/{id}/{type}', [
                                    Services\BusinessScheduleController::class, 'changeStatus'
                                ])->name('businessschedule.status');
                                Route::get('businessschedules/schedule/{scheduleid}', [
                                    Services\BusinessScheduleController::class, 'getSchedule'
                                ])->name('businessschedule.schedule');
                                Route::resource('/businessholidays', Dashboard\BusinessHolidayController::class);
                            });

                            Route::group(['middleware' => ['can:view_business']], function () {
                                Route::resource('social-links', Dashboard\Business\SocialLinkController::class);
                            });

                            // assign tags
                            Route::get('/admin-setting',  [Services\Business\AdminSettingsController::class, 'index'])->name('admin-settings');
                            Route::get('/chat-setting',  [Services\Business\AdminSettingsController::class, 'chatAdminSettings'])->name('chat-settings');
                            Route::post('enable/business-chat', [Services\Business\AdminSettingsController::class, 'enableDisableChat'])
                            ->name('enable.business-chat');
                            Route::get('business-level-three-tags', [Services\Business\AdminSettingsController::class, 'getLevelThreeTags'])->name('business-level-three-tags');
                            Route::get('assign/standard-tags/{tags}', [Services\Business\AdminSettingsController::class, 'assignTags'])
                                ->name('assign.standard-tags');
                            Route::post('/remove-product-tags',  [Services\Business\AdminSettingsController::class, 'removeProductTags'])->name('remove-product-tags');
                        });
                    });
            });
        Route::get('/dashboard/{type?}', [Services\PanelController::class, 'index'])
            ->name('dashboard.panel');
    });
