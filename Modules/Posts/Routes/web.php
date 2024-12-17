<?php

use Illuminate\Support\Facades\Route;
use Modules\Posts\Http\Controllers\Dashboard as Post;

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

Route::prefix('posts')
    ->as('posts.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::prefix('dashboard')
            ->as('dashboard.')
            ->group(function () {
                Route::prefix('/{moduleId}')
                    ->group(function () {
                        // tag hierarchies
                        Route::group(['middleware' => ['can:view_tag_hierarchies']], function () {
                            Route::resource('level/{level}/tag-hierarchies', Dashboard\HierarchiesController::class)->only(['index', 'store', 'show']);
                            Route::get('level-tags/{levelTwo}/{levelThree?}', [Post\HierarchiesController::class, 'getTagWithLevel'])->name('tag-hierarchies.getTagWithLevel');
                            Route::get('search/standardTags', [Post\HierarchiesController::class, 'searchStandardTags'])->name('search.standardTags');
                        });

                        //subscription plans
                        Route::group(['prefix' => 'subscription', 'as' => 'subscription.'], function () {
                            Route::resource('/plan', Dashboard\Subscription\PlanController::class)->middleware('can:view_subscription_plan');
                        });

                        Route::prefix('posts/{uuid}')->as('posts.')->group(function () {
                            // media
                            Route::group(['middleware' => 'can:view_product_images'], function () {
                                Route::resource('/media', Dashboard\Posts\PostMediaController::class)->except(['create', 'edit', 'show']);
                            });
                            // tags
                            Route::resource('tags', Dashboard\Posts\PostTagController::class)->only(['index']);
                            Route::get('tags/assign', [Post\Posts\PostTagController::class, 'assignTags'])->name('tags.assign');

                            // comments
                            Route::group(['middleware' => 'can:view_comment'], function () {
                                Route::resource('/comment', Dashboard\Posts\CommentController::class)->only(['show', 'destroy']);
                            });
                        });

                        // posts
                        Route::resource('/posts', Dashboard\Posts\PostController::class);
                        // post tags
                        Route::get('/tags/{tagId}/{level}', [Post\Posts\PostController::class, 'getTags'])->name('posts.tags');
                        // post status
                        Route::get('posts/status/{id}', [Post\Posts\PostController::class, 'changeStatus'])->name('posts.status');
                        // post all standard tags
                        Route::get('all/tags', [Post\Posts\PostController::class, 'searchPostTags'])->name('search.posts.tags');
                    });
            });

        Route::get('/dashboard/{type?}', [Post\PanelController::class, 'index'])->name('dashboard.panel');
    });
