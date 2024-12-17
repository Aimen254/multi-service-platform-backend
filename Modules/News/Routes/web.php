<?php

use Illuminate\Support\Facades\Route;
use Modules\News\Http\Controllers\Dashboard as News;

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

Route::prefix('news')
    ->as('news.')
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
                            Route::get('level-tags/{levelTwo}/{levelThree?}', [News\HierarchiesController::class, 'getTagWithLevel'])->name('tag-hierarchies.getTagWithLevel');
                            Route::get('search/standardTags', [News\HierarchiesController::class, 'searchStandardTags'])->name('search.standardTags');
                        });

                        Route::prefix('news/{uuid}')->as('news.')->group(function () {
                            // media
                            Route::group(['middleware' => 'can:view_product_images'], function () {
                                Route::resource('/media', Dashboard\News\NewsMediaController::class)->except(['create', 'edit', 'show']);
                            });
                            // tags
                            Route::resource('tags', Dashboard\News\NewsTagController::class)->only(['index']);
                            Route::get('tags/assign', [News\News\NewsTagController::class, 'assignTags'])->name('tags.assign');
                            // comments
                            Route::group(['middleware' => 'can:view_comment'], function () {
                                Route::resource('/comment', Dashboard\News\CommentController::class)->only(['show', 'destroy']);
                            });
                        });

                        // news
                        Route::resource('/news', Dashboard\News\NewsController::class);
                        // news tags
                        Route::get('/tags/{tagId}/{level}', [News\News\NewsController::class, 'getTags'])->name('news.tags');
                        // news status
                        Route::get('news/status/{id}', [News\News\NewsController::class, 'changeStatus'])->name('news.status');
                        // news all standard tags
                        Route::get('all/tags', [News\News\NewsController::class, 'searchNewsTags'])->name('search.news.tags');

                        //headlines
                        Route::group(['middleware' => ['can:view_headlines']], function () {
                            Route::resource('/headlines', Dashboard\HeadLineController::class);
                        });

                        // Headline settings
//                        Route::post('/headlines', [News\News\NewsController::class, 'makeHeadline'])->name('headlines.create');
//                        Route::delete('/remove-headline/{id}', [News\News\NewsController::class, 'removeHeadline'])->name('headlines.remove');
                    });
            });

        Route::get('/dashboard/{type?}', [News\PanelController::class, 'index'])->name('dashboard.panel');
    });
