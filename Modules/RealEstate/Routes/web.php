<?php

use Illuminate\Support\Facades\Route;
use Modules\RealEstate\Http\Controllers\Dashboard as RealEstate;
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

Route::prefix('real-estate')
    ->as('real-estate.')
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

                        // hierarchies management
                        Route::group(['middleware' => ['can:view_tag_hierarchies']], function () {
                            Route::resource('level/{level}/tag-hierarchies', Dashboard\HierarchiesController::class)->only(['index', 'store', 'show']);
                            Route::get('level-tags/{levelTwo}/{levelThree?}', [RealEstate\HierarchiesController::class, 'getTagWithLevel'])->name('tag-hierarchies.getTagWithLevel');
                            Route::get('search/standardTags', [RealEstate\HierarchiesController::class, 'searchStandardTags'])->name('search.standardTags');
                        });

                        // businesses
                        Route::group(['middleware' => ['can:view_business']], function () {
                            Route::resource('brokers', Dashboard\BusinessController::class);
                            Route::get('broker/change/status/{id}', [RealEstate\BusinessController::class, 'changeStatus'])->name('broker.status');
                        });
                        Route::group(['middleware' => ['can:approve_business']], function () {
                            Route::get('broker-request', [RealEstate\BusinessController::class, 'BrokerRequest'])->name('broker.request');
                            Route::get('broker/active/status/{id}', [RealEstate\BusinessController::class, 'activeStatus'])->name('broker.active.status');
                            Route::post('broker/reject/', [RealEstate\BusinessController::class, 'rejectBroker'])->name('broker.reject');
                        });

                        // business detail pages
                        Route::group(['prefix' => 'broker/{business_uuid?}', 'as' => 'broker.'], function () {
                            Route::group(['middleware' => ['can:view_business']], function () {
                                Route::resource('social-links', Dashboard\Business\SocialLinkController::class);
                            });

                            // Business Reviews
                            Route::group(['middleware' => ['can:view_reviews']], function () {
                                Route::resource('/reviews', Dashboard\ReviewController::class);
                                Route::get('review/status/{id}', [RealEstate\ReviewController::class, 'changeStatus'])->name('review.status');
                            });

                            // assign tags
                            Route::get('/admin-setting',  [RealEstate\Business\AdminSettingsController::class, 'index'])->name('admin-settings');
                            Route::get('/chat-setting',  [RealEstate\Business\AdminSettingsController::class, 'chatAdminSettings'])->name('chat-settings');
                            Route::post('enable/business-chat', [RealEstate\Business\AdminSettingsController::class, 'enableDisableChat'])
                            ->name('enable.business-chat');
                            Route::get('business-level-three-tags', [RealEstate\Business\AdminSettingsController::class, 'getLevelThreeTags'])->name('business-level-three-tags');
                            Route::post('/assign/standard-tags', [RealEstate\Business\AdminSettingsController::class, 'assignTags'])->name('assign.standard-tags');
                            Route::post('/remove-product-tags',  [RealEstate\Business\AdminSettingsController::class, 'removeProductTags'])->name('remove-product-tags');

                            // properties crud
                            Route::resource('properties', Dashboard\Property\PropertyController::class);
                            Route::get('tags/{tagId}/{level}', [RealEstate\Property\PropertyController::class, 'getTags'])->name('properties.tags');
                            Route::get('post/status/{id}', [RealEstate\Property\PropertyController::class, 'changeStatus'])->name('properties.status');
                            Route::get('all/tags', [RealEstate\Property\PropertyController::class, 'searchPropertyTags'])->name('search.properties.tags');
                            Route::get('agents', [RealEstate\Property\PropertyController::class, 'getAgents'])->name('properties.agents');

                            // communication portal
                            Route::group(['middleware' => ['can:view_contact_form']], function () {
                                Route::resource('/communication-portal', Dashboard\Property\ContactFormController::class);
                            });
                        });

                        Route::prefix('properties/{uuid?}')->as('properties.')->group(function () {
                            //property images
                            Route::group(['middleware' => ['can:view_product_images']], function () {
                                Route::resource('media', Dashboard\Property\PropertyMediaController::class);
                            });

                            // property tags routes
                            Route::resource('tags', Dashboard\Property\PropertyTagsController::class)->only(['index']);
                            Route::get('tags/assign', [RealEstate\Property\PropertyTagsController::class, 'assignTags'])->name('tags.assign');

                            // attributes tags
                            Route::get('attribute-tags/', [RealEstate\Property\PropertyAttributeTagsController::class, 'index'])->name('attribute-tags.index');
                            Route::post('attribute-tags/assign/', [RealEstate\Property\PropertyAttributeTagsController::class, 'assignTags'])->name('attribute-tags.assign');
                            Route::get('attribute-tags/search/', [RealEstate\Property\PropertyAttributeTagsController::class, 'searchTags'])->name('search.attribute.tags');
                        });

                        Route::prefix('{business_uuid?}')->group(function () {
                            // agents
                            Route::group(['middleware' => ['can:view_agents']], function () {
                                Route::resource('agents', Dashboard\AgentController::class)->except('show');
                                Route::get('agents/change/status/{id}', [RealEstate\AgentController::class, 'changeStatus'])
                                    ->name('agents.change.status');
                            });
                        });
                    });
            });
    });
