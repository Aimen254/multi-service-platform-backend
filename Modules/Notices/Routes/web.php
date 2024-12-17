<?php

use Illuminate\Support\Facades\Route;
use Modules\Notices\Http\Controllers\Dashboard as Notices;
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

Route::prefix('notices')
    ->as('notices.')
    ->middleware(['auth', 'verified'])
    ->group(
        function () {
            Route::prefix('dashboard')
                ->as('dashboard.')
                ->group(function () {
                    Route::prefix('/{moduleId}')
                        ->group(function () {

                            //subscription plans
                            Route::group(['prefix' => 'subscription', 'as' => 'subscription.'], function () {
                                Route::resource('/plan', Dashboard\Subscription\PlanController::class)->middleware('can:view_subscription_plan');
                            });

                            // hierarchies management
                            Route::group(['middleware' => ['can:view_tag_hierarchies']], function () {
                                Route::resource('level/{level}/tag-hierarchies', Dashboard\HierarchiesController::class)->only(['index', 'store', 'show']);
                                Route::get('level-tags/{levelTwo}/{levelThree?}', [Notices\HierarchiesController::class, 'getTagWithLevel'])->name('tag-hierarchies.getTagWithLevel');
                                Route::get('search/standardTags', [Notices\HierarchiesController::class, 'searchStandardTags'])->name('search.standardTags');
                            });

                            // businesses
                            Route::group(['middleware' => ['can:view_business']], function () {
                                Route::resource('organizations', Dashboard\BusinessController::class);
                                Route::get('organization/change/status/{id}', [Notices\BusinessController::class, 'changeStatus'])->name('organization.status');
                            });

                            Route::prefix('notices/{uuid}')->as('notices.')->group(function () {
                                // images
                                Route::group(['middleware' => ['can:view_product_images']], function () {
                                    Route::resource('media', Dashboard\Notice\NoticeMediaController::class);
                                });
                                // product tags routes
                                Route::resource('notice-tags', Dashboard\Notice\NoticeTagsController::class)->only(['index']);
                                Route::resource('reviews', Dashboard\Notice\NoticeReviewController::class);
                                Route::get('notice-tags/assign', [Notices\Notice\NoticeTagsController::class, 'assignTags'])->name('notice-tags.assign');
                            });

                            Route::group(['prefix' => 'organization/{business_uuid?}', 'as' => 'organization.'], function () {
                                Route::resource('notices', Dashboard\Notice\NoticeController::class);
                                Route::get('tags/{tagId}/{level}', [Notices\Notice\NoticeController::class, 'getTags'])->name('notices.tags');
                                Route::get('notices/status/{id}', [Notices\Notice\NoticeController::class, 'changeStatus'])->name('notices.status');

                                // Business Reviews
                                Route::group(['middleware' => ['can:view_reviews']], function () {
                                    Route::resource('/reviews', Dashboard\ReviewController::class);
                                    Route::get('review/status/{id}', [Notices\ReviewController::class, 'changeStatus'])->name('review.status');
                                });

                                //Business scheduling
                                Route::group(['middleware' => ['can:view_business_schedule_time']], function () {
                                    Route::resource('/organizationSchedules', Dashboard\BusinessScheduleController::class);
                                    Route::post('organizationSchedule/status/{id}/{type}', [Notices\BusinessScheduleController::class, 'changeStatus'])->name('organizationSchedule.status');
                                    Route::get('organizationSchedules/schedule/{scheduleid}', [Notices\BusinessScheduleController::class, 'getSchedule'])->name('organizationSchedules.schedule');
                                    Route::resource('/organizationsholidays', Dashboard\BusinessHolidayController::class);
                                });

                                Route::group(['middleware' => ['can:view_business']], function () {
                                    Route::resource('social-links', Dashboard\Business\SocialLinkController::class);
                                });

                                // assign tags
                                Route::get('/admin-setting',  [Notices\Business\AdminSettingsController::class, 'index'])->name('admin-settings');
                                Route::get('/chat-setting',  [Notices\Business\AdminSettingsController::class, 'chatAdminSettings'])->name('chat-settings');
                                Route::post('enable/business-chat', [Notices\Business\AdminSettingsController::class, 'enableDisableChat'])
                                ->name('enable.business-chat');
                                Route::get('business-level-three-tags', [Notices\Business\AdminSettingsController::class, 'getLevelThreeTags'])->name('business-level-three-tags');
                                Route::get('assign/standard-tags/{tags}', [Notices\Business\AdminSettingsController::class, 'assignTags'])
                                    ->name('assign.standard-tags');
                                Route::post('/remove-product-tags',  [Notices\Business\AdminSettingsController::class, 'removeProductTags'])->name('remove-product-tags');
                            });
                        });
                });


            // dashbord panel route
            Route::get('/dashboard/{type?}', [Notices\PanelController::class, 'index'])
                ->name('dashboard.panel');
        }
    );
