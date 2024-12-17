<?php

use Illuminate\Support\Facades\Route;
use Modules\Classifieds\Http\Controllers\Dashboard as Classifieds;

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


// Classifieds module prefix
Route::prefix('classifieds')
    ->as('classifieds.')
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
                            Route::resource('level/{level}/tag-hierarchies', Dashboard\HierarchiesController::class)->only(['index', 'store', 'show']);
                            Route::get('level-tags/{levelTwo}/{levelThree?}', [Classifieds\HierarchiesController::class, 'getTagWithLevel'])->name('tag-hierarchies.getTagWithLevel');
                            Route::get('search/standardTags', [Classifieds\HierarchiesController::class, 'searchStandardTags'])->name('search.standardTags');
                        });

                        Route::prefix('classifieds/{uuid}')->as('classifieds.')->group(function () {
                            // media
                            Route::group(['middleware' => 'can:view_product_images'], function () {
                                Route::resource('/media', Dashboard\Classifieds\ClassifiedMediaController::class)->except(['create', 'edit', 'show']);
                            });
                            // tags
                            Route::resource('tags', Dashboard\Classifieds\ClassifiedTagController::class)->only(['index']);
                            Route::get('tags/assign', [Classifieds\Classifieds\ClassifiedTagController::class, 'assignTags'])->name('tags.assign');
                            // attributes tags
                            Route::get('attribute-tags/', [Classifieds\Classifieds\ClassifiedAttributeTagsController::class, 'index'])->name('attribute-tags.index');
                            Route::get('attribute-tags/assign/', [Classifieds\Classifieds\ClassifiedAttributeTagsController::class, 'assignTags'])->name('attribute-tags.assign');
                            Route::get('attribute-tags/search/', [Classifieds\Classifieds\ClassifiedAttributeTagsController::class, 'searchTags'])->name('search.attribute.tags');
                            // reviews
                            Route::resource('reviews', Dashboard\Classifieds\ClassifiedReviewController::class)->only(['index', 'destroy']);
                            // comments
                            Route::group(['middleware' => 'can:view_comment'], function () {
                                Route::resource('/comments', Dashboard\Classifieds\ClassifiedCommentController::class)->only(['show', 'destroy']);
                            });
                        });

                        // classifieds
                        Route::resource('/classifieds', Dashboard\Classifieds\ClassifiedController::class);
                        // classifieds tags
                        Route::get('/tags/{tagId}/{level}', [Classifieds\Classifieds\ClassifiedController::class, 'getTags'])->name('classifieds.tags');
                        // classifieds status
                        Route::get('classifieds/status/{id}', [Classifieds\Classifieds\ClassifiedController::class, 'changeStatus'])->name('classifieds.status');
                        // classifieds all standard tags
                        Route::get('all/tags', [Classifieds\Classifieds\ClassifiedController::class, 'searchClassifiedTags'])->name('search.classifieds.tags');

                        // classifieds communication portal
                        Route::group(['middleware' => ['can:view_contact_form']], function () {
                            Route::resource('/communication-portal', Dashboard\Classifieds\ContactFormController::class);
                        });
                    });
            });

        Route::get('/dashboard/{type?}', [Classifieds\PanelController::class, 'index'])->name('dashboard.panel');
    });
