<?php

use Illuminate\Support\Facades\Route;
use Modules\Government\Http\Controllers\Dashboard as Government;
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

Route::prefix('government')
    ->as('government.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::prefix('dashboard')->as('dashboard.')->group(function () {
            Route::prefix('/{moduleId}')->group(function () {

                //subscription plans
                Route::group(['prefix' => 'subscription', 'as' => 'subscription.'], function () {
                    Route::resource('/plan', Dashboard\Subscription\PlanController::class)->middleware('can:view_subscription_plan');
                });

                /*
                * government tag hierarchies
                */
                Route::group(['middleware' => ['can:view_tag_hierarchies']], function () {
                    Route::resource('level/{level}/tag-hierarchies', Dashboard\HierarchiesController::class)->only(['index', 'store', 'show']);
                    Route::get('level-tags/{levelTwo}/{levelThree?}', [Government\HierarchiesController::class, 'getTagWithLevel'])->name('tag-hierarchies.getTagWithLevel');
                    Route::get('search/standardTags', [Government\HierarchiesController::class, 'searchStandardTags'])->name('search.standardTags');
                });

                // governemt depratments
                Route::group(['middleware' => ['can:view_business']], function () {
                    Route::resource('departments', Dashboard\DepartmentController::class);
                    Route::get('department/change/status/{id}', [Government\DepartmentController::class, 'changeStatus'])->name('department.status');
                });

                Route::group(['prefix' => 'department/{business_uuid?}', 'as' => 'department.'], function () {
                    // Department Reviews
                    Route::group(['middleware' => ['can:view_reviews']], function () {
                        Route::resource('/reviews', Dashboard\ReviewController::class);
                        Route::get('review/status/{id}', [Government\ReviewController::class, 'changeStatus'])->name('review.status');
                    });
                    Route::group(['middleware' => ['can:view_government_staff']], function () {
                        Route::resource('staffs', Dashboard\GovernmentstaffController::class);
                        Route::get('staff/change/status/{id}', [Government\GovernmentstaffController::class, 'changeStatus'])
                            ->name('agents.change.status');
                    });

                    //Business scheduling
                    Route::group(['middleware' => ['can:view_business_schedule_time']], function () {
                        Route::resource('/departmentschedule', Dashboard\DepartmentScheduleController::class);
                        Route::post('departmentschedule/status/{id}/{type}', [
                            Government\DepartmentScheduleController::class, 'changeStatus'
                        ])->name('departmentschedule.status');
                        Route::get('departmentschedules/schedule/{scheduleid}', [
                            Government\DepartmentScheduleController::class, 'getSchedule'
                        ])->name('departmentschedule.schedule');
                        Route::resource('/departmentholidays', Dashboard\DepartmentHolidayController::class);
                    });

                    Route::group(['middleware' => ['can:view_business']], function () {
                        Route::resource('social-links', Dashboard\SocialLinkController::class);
                    });

                    // business admin settings
                    Route::group(['middleware' => ['can:view_admin_settings']], function () {
                        Route::get('/admin-setting',  [Government\SettingsController::class, 'standardTagsAdminSettings'])->name('admin-settings');
                        Route::get('/chat-setting',  [Government\SettingsController::class, 'chatAdminSettings'])->name('chat-settings');
                        Route::post('enable/business-chat', [Government\SettingsController::class, 'enableDisableChat'])
                        ->name('enable.business-chat');
                        Route::get('assign/standard-tags/{tags}', [Government\SettingsController::class, 'assignTags'])
                            ->name('assign.standard-tags');
                        Route::post(
                            '/remove-product-tags',
                            [Government\SettingsController::class, 'removeProductTags']
                        )->name('remove-product-tags');
                        Route::get('business-level-three-tags', [Government\SettingsController::class, 'getLevelThreeTags'])->name('business-level-three-tags');
                    });

                    //Business scheduling
                    Route::group(['middleware' => ['can:view_business_schedule_time']], function () {
                        Route::resource('/departmentschedules', Dashboard\DepartmentScheduleController::class);
                        Route::post('businessschedule/status/{id}/{type}', [
                            Retail\Dashboard\BusinessScheduleController::class, 'changeStatus'
                        ])->name('businessschedule.status');
                        Route::get('businessschedules/schedule/{scheduleid}', [
                            Retail\Dashboard\BusinessScheduleController::class, 'getSchedule'
                        ])->name('businessschedule.schedule');
                        Route::resource('/businessholidays', Dashboard\BusinessHolidayController::class);
                    });

                    // social Links
                    Route::group(['middleware' => ['can:view_business']], function () {
                        Route::resource('social-links', Dashboard\SocialLinkController::class);
                    });
                });
            });
        });

        Route::prefix('dashboard/{moduleId?}')
            ->as('dashboard.')->group(function () {
                Route::prefix('/department/{business_uuid?}')->as('department.')->group(function () {
                    Route::group(['middleware' => ['can:view_contact_form']], function () {
                        Route::resource('/communication-portal', Dashboard\Post\ContactFormController::class);
                    });
                    Route::resource('posts', Dashboard\Post\PostController::class);
                    Route::get('tags/{tagId}/{level}', [Government\Post\PostController::class, 'getTags'])->name('post.tags');
                    Route::get('post/status/{id}', [Government\Post\PostController::class, 'changeStatus'])->name('post.status');
                    Route::get('all/tags', [Government\Post\PostController::class, 'searchPostTags'])->name('search.post.tags');
                });

                Route::prefix('post/{uuid?}')->as('post.')->group(function () {
                    //post images
                    Route::group(['middleware' => ['can:view_product_images']], function () {
                        Route::resource('media', Dashboard\Post\PostMediaController::class);
                    });

                    // post tags routes
                    Route::resource('post-tags', Dashboard\Post\PostTagsController::class)->only(['index']);
                    Route::resource('reviews', Dashboard\Post\PostReviewController::class);
                    Route::get('post-tags/assign', [Government\Post\PostTagsController::class, 'assignTags'])->name('post-tags.assign');

                    // comments
                    Route::resource('comments', Dashboard\Post\CommentController::class);
                });
            });
        Route::get('/dashboard/{type?}', [Government\PanelController::class, 'index'])
            ->name('dashboard.panel');
    });
