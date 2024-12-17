<?php

use Illuminate\Support\Facades\Route;
use Modules\Blogs\Http\Controllers\Dashboard as Blogs;

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

// Blogs module prefix
Route::prefix('blogs')
    ->as('blogs.')
    ->middleware(['auth'])
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
                            Route::resource('level/{level}/tag-hierarchies', Dashboard\HierarchiesController::class)->only(['index', 'store', 'show']);
                            Route::get('level-tags/{levelTwo}/{levelThree?}', [Blogs\HierarchiesController::class, 'getTagWithLevel'])->name('tag-hierarchies.getTagWithLevel');
                            Route::get('search/standardTags', [Blogs\HierarchiesController::class, 'searchStandardTags'])->name('search.standardTags');
                        });

                        //headlines
                        Route::group(['middleware' => ['can:view_headlines']], function () {
                            Route::resource('/headlines', Dashboard\HeadLineController::class);
                        });

                        Route::prefix('blogs/{uuid}')->as('blogs.')->group(function () {
                            // media
                            Route::group(['middleware' => 'can:view_product_images'], function () {
                                Route::resource('/media', Dashboard\Blogs\BlogMediaController::class)->except(['create', 'edit', 'show']);
                            });
                            // tags
                            Route::resource('tags', Dashboard\Blogs\BlogTagController::class)->only(['index']);
                            Route::get('tags/assign', [Blogs\Blogs\BlogTagController::class, 'assignTags'])->name('tags.assign');
                            // comments
                            Route::group(['middleware' => 'can:view_comment'], function () {
                                Route::resource('/comments', Dashboard\Blogs\BlogCommentController::class)->only(['show', 'destroy']);
                            });
                        });

                        // blog
                        Route::resource('/blogs', Dashboard\Blogs\BlogController::class);
                        // blog tags
                        Route::get('/tags/{tagId}/{level}', [Blogs\Blogs\BlogController::class, 'getTags'])->name('blogs.tags');
                        // blog status
                        Route::get('blogs/status/{id}', [Blogs\Blogs\BlogController::class, 'changeStatus'])->name('blogs.status');
                        // blog all standard tags
                        Route::get('all/tags', [Blogs\Blogs\BlogController::class, 'searchBlogTags'])->name('search.blogs.tags');
                    });
            });

        Route::get('/dashboard/{type?}', [Blogs\PanelController::class, 'index'])->name('dashboard.panel');
    });
