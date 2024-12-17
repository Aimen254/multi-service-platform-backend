<?php

use Illuminate\Support\Facades\Route;
use Modules\Events\Http\Controllers\Dashboard as Events;

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

Route::prefix('events')
    ->as('events.')
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
                            Route::get('level-tags/{levelTwo}/{levelThree?}', [Events\HierarchiesController::class, 'getTagWithLevel'])->name('tag-hierarchies.getTagWithLevel');
                            Route::get('search/standardTags', [Events\HierarchiesController::class, 'searchStandardTags'])->name('search.standardTags');
                        });


                        Route::prefix('events/{uuid}')->as('events.')->group(function () {
                            // media
                            Route::group(['middleware' => 'can:view_product_images'], function () {
                                Route::resource('/media',Dashboard\Events\EventMediaController::class)->except(['create', 'edit', 'show']);
                            });
                            
                            // tags
                            Route::resource('tags', Dashboard\Events\EventTagController::class)->only(['index']);
                            Route::get('tags/assign', [Events\Events\EventTagController::class, 'assignTags'])->name('tags.assign');

                            Route::get('attribute-tags/', [Events\Events\EventAttributeController::class, 'index'])->name('attribute-tags.index');
                            Route::get('attribute-tags/assign/', [Events\Events\EventAttributeController::class, 'assignTags'])->name('attribute-tags.assign');
                            Route::get('attribute-tags/search/', [Events\Events\EventAttributeController::class, 'searchTags'])->name('search.attribute.tags');
                        
                            // comments
                          
                        });


                            Route::resource('events', Dashboard\Events\EventController::class);
                            Route::get('/tags/{tagId}/{level}', [Events\Events\EventController::class,'getTags'])->name('events.tags');

                            Route::get('events/status/{id}', [Events\Events\EventController::class, 'changeStatus'])->name('events.status');
                            // blog all standard tags
                            Route::get('all/tags', [Events\Events\EventController::class, 'searchEventsTags'])->name('search.events.tags');
                    });
            });
    });
