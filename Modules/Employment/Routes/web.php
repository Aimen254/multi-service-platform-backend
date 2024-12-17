<?php

use Illuminate\Support\Facades\Route;
use Modules\Employment\Http\Controllers\Dashboard as Employment;
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

Route::prefix('employment')
    ->as('employment.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::prefix('dashboard')
            ->as('dashboard.')
            ->group(function () {
                Route::prefix('/{moduleId}')
                    ->group(function () {
                        /*
                        * employement tag hierarchies
                        */
                        Route::group(['middleware' => ['can:view_tag_hierarchies']], function () {
                            Route::resource('level/{level}/tag-hierarchies', Dashboard\HierarchiesController::class)->only(['index', 'store', 'show']);
                            Route::get('level-tags/{levelTwo}/{levelThree?}', [Employment\HierarchiesController::class, 'getTagWithLevel'])->name('tag-hierarchies.getTagWithLevel');
                            Route::get('search/standardTags', [Employment\HierarchiesController::class, 'searchStandardTags'])->name('search.standardTags');
                        });

                        //subscription plans
                        Route::group([
                            'prefix' => 'subscription', 'as' => 'subscription.'
                        ], function () {
                            Route::resource('/plan', Dashboard\Subscription\PlanController::class)->middleware('can:view_subscription_plan');
                        });

                        // employers routes
                        Route::group(['middleware' => ['can:view_business']], function () {
                            Route::resource('employers', Dashboard\EmployerController::class);
                            Route::get('employers/change/status/{id}', [Employment\EmployerController::class, 'changeStatus'])->name('employers.status');
                        });


                        // employer detail
                        Route::group(['prefix' => 'employers/{business_uuid?}', 'as' => 'employers.'], function () {
                            // soscial links
                            Route::group(['middleware' => ['can:view_business']], function () {
                                Route::resource('social-links', Dashboard\Employer\SocialLinksController::class);
                            });

                            //  employer Reviews
                            Route::group(['middleware' => ['can:view_reviews']], function () {
                                Route::resource('/reviews', Dashboard\ReviewController::class);
                                Route::get('review/status/{id}', [Employment\ReviewController::class, 'changeStatus'])->name('review.status');
                            });

                            //Business scheduling
                            Route::group(['middleware' => ['can:view_business_schedule_time']], function () {
                                Route::resource('/employerschedule', Dashboard\EmployerScheduleController::class);
                                Route::post('employerschedule/status/{id}/{type}', [
                                    Employment\EmployerScheduleController::class, 'changeStatus'
                                ])->name('employerschedule.status');
                                Route::get('employerschedules/schedule/{scheduleid}', [
                                    Employment\EmployerScheduleController::class, 'getSchedule'
                                ])->name('employerschedule.schedule');
                                Route::resource('/employerholidays', Dashboard\EmployerHolidayController::class);
                            });

                            // Employer admin settings
                            Route::group(
                                ['middleware' => ['can:view_admin_settings']],
                                function () {
                                    Route::get('/admin-setting',  [Employment\SettingsController::class, 'standardTagsAdminSettings'])->name('admin-settings');
                                    Route::get('/chat-setting',  [Employment\SettingsController::class, 'chatAdminSettings'])->name('chat-settings');
                                    Route::post('enable/business-chat', [Employment\SettingsController::class, 'enableDisableChat'])
                                    ->name('enable.business-chat');
                                    Route::get('assign/standard-tags/{tags}', [Employment\SettingsController::class, 'assignTags'])
                                        ->name('assign.standard-tags');
                                    Route::post('/remove-product-tags',  [Employment\SettingsController::class, 'removeProductTags'])->name('remove-product-tags');
                                    Route::get('business-level-three-tags', [Employment\SettingsController::class, 'getLevelThreeTags'])->name('business-level-three-tags');
                                }
                            );
                        });
                    });
            });

        Route::prefix('dashboard/{moduleId?}')
            ->as('dashboard.')->group(function () {
                Route::prefix('/employers/{business_uuid?}')->as('employers.')->group(function () {
                    Route::resource('posts', Dashboard\Post\PostController::class);
                    Route::get('tags/{tagId}/{level}', [Employment\Post\PostController::class, 'getTags'])->name('post.tags');
                    Route::get('post/status/{id}', [Employment\Post\PostController::class, 'changeStatus'])->name('post.status');
                    Route::get('all/tags', [Employment\Post\PostController::class, 'searchPostTags'])->name('search.post.tags');
                });

                Route::prefix('post/{uuid}')->as('post.')->group(function () {
                    //post images
                    Route::group(['middleware' => ['can:view_product_images']], function () {
                        Route::resource('media', Dashboard\Post\PostMediaController::class);
                    });

                    // post tags routes
                    Route::resource('post-tags', Dashboard\Post\PostTagsController::class)->only(['index']);
                    Route::resource('reviews', Dashboard\Post\PostReviewController::class);
                    Route::get('post-tags/assign', [Employment\Post\PostTagsController::class, 'assignTags'])->name('post-tags.assign');

                });
            });
        Route::get('/dashboard/{type?}', [Employment\PanelController::class, 'index'])
            ->name('dashboard.panel');
    });
