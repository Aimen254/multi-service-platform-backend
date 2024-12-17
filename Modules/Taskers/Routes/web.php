<?php

use Illuminate\Support\Facades\Route;
use Modules\Taskers\Http\Controllers\Dashboard as Taskers;

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

// Taskers module prefix
Route::prefix('taskers')
    ->as('taskers.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        // Dashboard prefix
        Route::prefix('dashboard')
            ->as('dashboard.')
            ->group(function () {
                // Module id prefix
                Route::prefix('/{moduleId}')
                    ->group(function () {
                        //subscription plans
                        Route::group(['prefix' => 'subscription', 'as' => 'subscription.'], function () {
                            Route::resource('/plan', Dashboard\Subscription\PlanController::class)->middleware('can:view_subscription_plan');
                        });

                        // Tag hierarchies
                        Route::group(['middleware' => ['can:view_tag_hierarchies']], function () {
                            Route::resource('level/{level}/tag-hierarchies', Dashboard\HierarchyController::class)->only(['index', 'store', 'show']);
                            Route::get('level-tags/{levelTwo}/{levelThree?}', [Taskers\HierarchyController::class, 'getTagWithLevel'])->name('tag-hierarchies.getTagWithLevel');
                            Route::get('search/standardTags', [Taskers\HierarchyController::class, 'searchStandardTags'])->name('search.standardTags');
                        });

                        Route::prefix('taskers/{uuid}')->as('taskers.')->group(function () {
                            // media
                            Route::group(['middleware' => 'can:view_product_images'], function () {
                                Route::resource('/media', Dashboard\Taskers\TaskerMediaController::class)->except(['create', 'edit', 'show']);
                            });

                            // tags
                            Route::resource('tags', Dashboard\Taskers\TaskerTagController::class)->only(['index']);
                            Route::get('tags/assign', [Taskers\Taskers\TaskerTagController::class, 'assignTags'])->name('tags.assign');

                            // reviews
                            Route::resource('reviews', Dashboard\Taskers\TaskerReviewController::class)->only(['index', 'destroy']);
                        });

                        // user settings
                        Route::prefix('{userId}')->group( function () {
                            Route::get('/user-setting',  [Taskers\SettingsController::class, 'standardTagsUserSettings'])->name('user-settings');
                            Route::get('assign/standard-tags/{tags}', [Taskers\SettingsController::class, 'assignTags'])->name('assign.standard-tags');
                            Route::post('/remove-product-tags', [Taskers\SettingsController::class, 'removeProductTags'])->name('remove-product-tags');
                            Route::get('user-level-three-tags', [Taskers\SettingsController::class, 'getLevelThreeTags'])->name('user-level-three-tags');
                        });

                        // taskers
                        Route::resource('/taskers', Dashboard\Taskers\TaskerController::class);
                        // taskers tags
                        Route::get('/tags/{tagId}/{level}', [Taskers\Taskers\TaskerController::class, 'getTags'])->name('taskers.tags');
                        // taskers status
                        Route::get('taskers/status/{id}', [Taskers\Taskers\TaskerController::class, 'changeStatus'])->name('taskers.status');
                        // taskers all standard tags
                        Route::get('all/tags', [Taskers\Taskers\TaskerController::class, 'searchTaskerTags'])->name('search.taskers.tags');

                        // taskers communication portal
                        Route::group(['middleware' => ['can:view_contact_form']], function () {
                            Route::resource('/communication-portal', Dashboard\Taskers\ContactFormController::class);
                        });
                    });
            });

        Route::get('/dashboard/{type?}', [Taskers\PanelController::class, 'index'])->name('dashboard.panel');
    });
