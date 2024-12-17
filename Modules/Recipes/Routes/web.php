<?php

use Illuminate\Support\Facades\Route;
use Modules\Recipes\Http\Controllers\Dashboard as Recipes;

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

// Recipes module prefix
Route::prefix('recipes')
    ->as('recipes.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        // dashboard prefix
        Route::prefix('dashboard')
            ->as('dashboard.')
            ->group(function () {
                // module id prefix
                Route::prefix('/{moduleId}')
                    ->group(function () {
                        //subscription plans
                        Route::group(['prefix' => 'subscription', 'as' => 'subscription.'], function () {
                            Route::resource('/plan', Dashboard\Subscription\PlanController::class)->middleware('can:view_subscription_plan');
                        });

                        // tag hierarchies
                        Route::group(['middleware' => ['can:view_tag_hierarchies']], function () {
                            Route::resource('level/{level}/tag-hierarchies', Dashboard\HierarchiesController::class)->only(['index', 'store', 'show']);
                            Route::get('level-tags/{levelTwo}/{levelThree?}', [Recipes\HierarchiesController::class, 'getTagWithLevel'])->name('tag-hierarchies.getTagWithLevel');
                            Route::get('search/standardTags', [Recipes\HierarchiesController::class, 'searchStandardTags'])->name('search.standardTags');
                        });

                        Route::prefix('recipes/{uuid}')->as('recipes.')->group(function () {
                            // media
                            Route::group(['middleware' => 'can:view_product_images'], function () {
                                Route::resource('/media', Dashboard\Recipes\RecipesMediaController::class)->except(['create', 'edit', 'show']);
                            });
                            // tags
                            Route::resource('tags', Dashboard\Recipes\RecipesTagController::class)->only(['index']);
                            Route::get('tags/assign', [Recipes\Recipes\RecipesTagController::class, 'assignTags'])->name('tags.assign');
                            // comments
                            Route::group(['middleware' => 'can:view_comment'], function () {
                                Route::resource('/comments', Dashboard\Recipes\RecipeCommentController::class)->only(['show', 'destroy']);
                            });
                        });

                        // recipe
                        Route::resource('/recipes', Dashboard\Recipes\RecipesController::class);
                        // recipe tags
                        Route::get('/tags/{tagId}/{level}', [Recipes\Recipes\RecipesController::class, 'getTags'])->name('recipes.tags');
                        // recipe status
                        Route::get('news/status/{id}', [Recipes\Recipes\RecipesController::class, 'changeStatus'])->name('recipes.status');
                        // recipe all standard tags
                        Route::get('all/tags', [Recipes\Recipes\RecipesController::class, 'searchRecipesTags'])->name('search.recipes.tags');

                        //headlines
                        Route::group(['middleware' => ['can:view_headlines']], function () {
                            Route::resource('/headlines', Dashboard\HeadLineController::class);
                        });

                        // Headline settings
//                        Route::post('/headlines', [Recipes\Recipes\RecipesController::class, 'makeHeadline'])->name('headlines.create');
//                        Route::delete('delete-headline/{id}', [Recipes\Recipes\RecipesController::class, 'deleteHeadline'])->name('headlines.delete');
                    });
            });
        Route::get('/dashboard/{type?}', [Recipes\PanelController::class, 'index'])->name('dashboard.panel');
    });
