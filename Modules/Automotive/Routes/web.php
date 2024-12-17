<?php

use Illuminate\Support\Facades\Route;
use Dashboard\Vehicle\VehicleController;
use Modules\Automotive\Http\Controllers\Dashboard as Automotive;

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

Route::prefix('automotive')
    ->as('automotive.')
    ->middleware(['auth'])
    ->group(function () {
        Route::prefix('dashboard')
            ->as('dashboard.')
            ->group(function () {
                Route::prefix('/{moduleId}')
                    ->group(function () {
                        //Garages
                        Route::group(['middleware' => ['can:view_business']], function () {
                            Route::resource('dealership', Dashboard\GarageController::class);
                            Route::get('dealership/change/status/{id}', [Automotive\GarageController::class, 'changeStatus'])->name('dealership.status');
                        });
                        Route::group(['prefix' => 'dealership/{business_uuid?}', 'as' => 'dealership.'], function () {
                            // Vehicle Contact Form
                            Route::group(['middleware' => ['can:view_contact_form']], function () {
                                Route::resource('/contact-form', Dashboard\Vehicle\ContactFormController::class);
                            });
                            //garage admin settings
                            Route::group(['middleware' => ['can:view_admin_settings']], function () {
                                Route::get('/admin-setting',  [Automotive\SettingsController::class, 'standardTagsAdminSettings'])->name('admin-settings');
                                Route::post('assign/standard-tags', [Automotive\SettingsController::class, 'assignTags'])
                                    ->name('assign.standard-tags');
                                Route::post('/filter-industry-tags',  [Automotive\SettingsController::class, 'filterIndustryTags'])->name('industryTags');
                                Route::post('/remove-product-tags',  [Automotive\SettingsController::class, 'removeProductTags'])->name('remove-product-tags');
                                Route::post('/delete-tags/{tag_id?}',  [Automotive\SettingsController::class, 'deleteTags'])->name('delete-tags');
                                Route::get('business-level-three-tags', [Automotive\SettingsController::class, 'getLevelThreeTags'])->name('business-level-three-tags');
                                Route::get('/chat-setting',  [Automotive\SettingsController::class, 'chatAdminSettings'])->name('chat-settings');
                                Route::post('enable/business-chat', [Automotive\SettingsController::class, 'enableDisableChat'])
                                ->name('enable.business-chat');
                            });
                            // Business Reviews
                            Route::group(['middleware' => ['can:view_reviews']], function () {
                                Route::resource('/reviews', Dashboard\ReviewController::class);
                                Route::get('review/status/{id}', [Automotive\ReviewController::class, 'changeStatus'])->name('review.status');
                            });
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
                * automotive tag hierarchies
                */
                Route::group(['middleware' => ['can:view_tag_hierarchies']], function () {
                    Route::resource('level/{level}/tag-hierarchies', Dashboard\HierarchiesController::class)->only(['index', 'store', 'show']);
                    Route::get('level-tags/{levelTwo}/{levelThree?}', [Automotive\HierarchiesController::class, 'getTagWithLevel'])->name('tag-hierarchies.getTagWithLevel');
                    Route::get('search/standardTags', [Automotive\HierarchiesController::class, 'searchStandardTags'])->name('search.standardTags');
                });

                // vehicles routes
                Route::prefix('/dealership/{garageId?}')->as('dealership.')->group(function () {
                    Route::resource('vehicles', Dashboard\Vehicle\VehicleController::class);
                    Route::get('tags/{tagId}/{level}', [Automotive\Vehicle\VehicleController::class, 'getTags'])->name('vehicle.tags');
                    Route::post('vehicle/status/{id}', [Automotive\Vehicle\VehicleController::class, 'changeStatus'])->name('vehicle.status');
                    Route::get('all/tags', [Automotive\Vehicle\VehicleController::class, 'searchVehicleTags'])->name('search.vehicle.tags');
                });

                Route::prefix('vehicle/{uuid}')->as('vehicle.')->group(function () {
                    //vehicles images
                    Route::group(['middleware' => ['can:view_product_images']], function () {
                        Route::resource('media', Dashboard\Vehicle\VehicleMediaController::class);
                    });
                    // product tags routes
                    Route::resource('vehicle-tags', Dashboard\Vehicle\VehicleTagsController::class)->only(['index']);
                    Route::get('vehicle-tags/assign', [Automotive\Vehicle\VehicleTagsController::class, 'assignTags'])->name('vehicle-tags.assign');

                    // product attributes tags section
                    Route::get('attribute-tags/', [Automotive\Vehicle\AttributeTagsController::class, 'index'])->name('attribute-tags.index');
                    Route::get('attribute-tags/assign/', [Automotive\Vehicle\AttributeTagsController::class, 'assignTags'])->name('attribute-tags.assign');
                    Route::get('attribute-tags/search/', [Automotive\Vehicle\AttributeTagsController::class, 'searchTags'])->name('search.attribute.tags');
                });
            });
        // panel route at the end
        Route::get('/dashboard/{type?}', [Automotive\PanelController::class, 'index'])
            ->name('dashboard.panel');
    });
