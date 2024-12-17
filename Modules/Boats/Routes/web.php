<?php

use Illuminate\Support\Facades\Route;
use Modules\Boats\Http\Controllers\Dashboard as Boats;
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

Route::prefix('boats')
    ->as('boats.')
    ->middleware(['auth'])
    ->group(function () {
        Route::prefix('dashboard')->as('dashboard.')->group(function () {
            Route::prefix('/{moduleId}')->group(function () {
                Route::group(['middleware' => ['can:view_business']], function () {
                    Route::resource('dealership', Dashboard\DealershipController::class);
                    Route::get('dealership/change/status/{id}', [Boats\DealershipController::class, 'changeStatus'])->name('dealership.status');
                });

                Route::group(['prefix' => 'dealership/{business_uuid?}', 'as' => 'dealership.'], function () {

                    // boat contact form
                    Route::group(['middleware' => ['can:view_contact_form']], function () {
                        Route::resource('/contact-form', Dashboard\Boat\ContactFormController::class);
                    });
                    // Business Reviews
                    Route::group(['middleware' => ['can:view_reviews']], function () {
                        Route::resource('/reviews', Dashboard\ReviewController::class);
                        Route::get('review/status/{id}', [Boats\ReviewController::class, 'changeStatus'])->name('review.status');
                    });

                    // Dealership admin settings
                    Route::group(
                        ['middleware' => ['can:view_admin_settings']],
                        function () {
                            Route::get('/admin-setting',  [Boats\SettingsController::class, 'index'])->name('admin-settings');
                            Route::get('/chat-setting',  [Boats\SettingsController::class, 'chatAdminSettings'])->name('chat-settings');
                            Route::post('enable/business-chat', [Boats\SettingsController::class, 'enableDisableChat'])
                            ->name('enable.business-chat');
                            Route::post('assign/standard-tags', [Boats\SettingsController::class, 'assignTags'])
                                ->name('assign.standard-tags');
                            Route::post('/remove-product-tags',  [Boats\SettingsController::class, 'removeProductTags'])->name('remove-product-tags');
                            Route::get('business-level-three-tags', [Boats\SettingsController::class, 'getLevelThreeTags'])->name('business-level-three-tags');
                        }
                    );
                });
                 //subscription plans
                 Route::group(['prefix' => 'subscription', 'as' => 'subscription.'], function () {
                    Route::resource('/plan', Dashboard\Subscription\PlanController::class)->middleware('can:view_subscription_plan');
                });
            });
        });

        Route::prefix('dashboard/{moduleId?}')
            ->as('dashboard.')->group(function () {
                /*
                * boats tag hierarchies
                */
                Route::group(['middleware' => ['can:view_tag_hierarchies']], function () {
                    Route::resource('level/{level}/tag-hierarchies', Dashboard\HierarchiesController::class)->only(['index', 'store', 'show']);
                    Route::get('level-tags/{levelTwo}/{levelThree?}', [Boats\HierarchiesController::class, 'getTagWithLevel'])->name('tag-hierarchies.getTagWithLevel');
                    Route::get('search/standardTags', [Boats\HierarchiesController::class, 'searchStandardTags'])->name('search.standardTags');
                });

                // boats routes
                Route::prefix('/dealership/{garageId?}')->as('dealership.')->group(function () {
                    Route::resource('boats', Dashboard\Boat\BoatController::class);
                    Route::get('tags/{tagId}/{level}', [Boats\Boat\BoatController::class, 'getTags'])->name('boat.tags');
                    Route::post('boat/status/{id}', [Boats\Boat\BoatController::class, 'changeStatus'])->name('boat.status');
                });

                Route::prefix('boat/{uuid}')->as('boat.')->group(function () {
                    //boat images
                    Route::group(['middleware' => ['can:view_product_images']], function () {
                        Route::resource('media', Dashboard\Boat\BoatMediaController::class);
                    });

                    // product tags routes
                    Route::resource('boat-tags', Dashboard\Boat\BoatTagsController::class)->only(['index']);
                    Route::get('boat-tags/assign', [Boats\Boat\BoatTagsController::class, 'assignTags'])->name('boat-tags.assign');

                    // product attributes tags section
                    Route::get('attribute-tags/', [Boats\Boat\AttributeTagsController::class, 'index'])->name('attribute-tags.index');
                    Route::get('attribute-tags/assign/', [Boats\Boat\AttributeTagsController::class, 'assignTags'])->name('attribute-tags.assign');
                    Route::get('attribute-tags/search/', [Boats\Boat\AttributeTagsController::class, 'searchTags'])->name('search.attribute.tags');
                });
            });

        Route::get('/dashboard/{type?}', [Boats\PanelController::class, 'index'])
            ->name('dashboard.panel');
    });
