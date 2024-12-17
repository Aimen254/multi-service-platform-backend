<?php

use Illuminate\Support\Facades\Route;
use Modules\Obituaries\Http\Controllers\Dashboard as Obituaries;
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

Route::prefix('obituaries')
    ->as('obituaries.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::prefix('dashboard')
            ->as('dashboard.')
            ->group(function () {
                Route::prefix('/{moduleId}')
                    ->group(function () {
                        Route::prefix('obituaries/{uuid}')->as('obituaries.')->group(function () {
                            // comments
                            Route::group(['middleware' => 'can:view_comment'], function () {
                                Route::resource('comments', Dashboard\Obituaries\CommentController::class);
                            });
                        });
                        Route::group(['prefix' => 'subscription', 'as' => 'subscription.'], function () {
                            Route::resource('/plan', Dashboard\Subscription\PlanController::class)->middleware('can:view_subscription_plan');
                        });
                        Route::group(['middleware' => ['can:view_tag_hierarchies']], function () {
                            Route::resource('level/{level}/tag-hierarchies', Dashboard\HierarchiesController::class)->only(['index', 'store', 'show']);
                            Route::get('level-tags/{levelTwo}/{levelThree?}', [Obituaries\HierarchiesController::class, 'getTagWithLevel'])->name('tag-hierarchies.getTagWithLevel');
                            Route::get('search/standardTags', [Obituaries\HierarchiesController::class, 'searchStandardTags'])->name('search.standardTags');
                        });
                        Route::prefix('obituaries/{uuid}')->as('obituaries.')->group(function () {
                            // media
                            Route::group(['middleware' => 'can:view_product_images'], function () {
                                Route::resource('/media', Dashboard\Obituaries\ObituariesMediaController::class)->except(['create', 'edit', 'show']);
                            });
                            // tags
                            Route::resource('tags', Dashboard\Obituaries\ObituariesTagController::class)->only(['index']);
                            Route::get('tags/assign', [Obituaries\Obituaries\ObituariesTagController::class, 'assignTags'])->name('tags.assign');
                        });

                        // Obituaries
                        Route::resource('/obituaries', Dashboard\Obituaries\ObituariesController::class);
                        Route::get('/tags/{tagId}/{level}', [Obituaries\Obituaries\ObituariesController::class, 'getTags'])->name('obituaries.tags');
                        Route::get('obituaries/status/{id}', [Obituaries\Obituaries\ObituariesController::class, 'changeStatus'])->name('obituaries.status');

                        Route::get('all/tags', [Obituaries\Obituaries\ObituariesController::class, 'searchObituariesTags'])->name('search.obituaries.tags');
                    });
            });
        Route::get('/dashboard/{type?}', [Obituaries\PanelController::class, 'index'])->name('dashboard.panel');
    });
