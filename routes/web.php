<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin as Admin;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

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

Route::get('scrape',  [Admin\Scraper\ScraperController::class, 'index']);
Route::post('stripe-webhooks', [Admin\StripeWebhooksController::class, 'paymentIntentSucceeded'])->name('stripe-webhooks');
Route::get('/', function () {
    return redirect()->route('dashboard');
});
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', ['type' => request()->type]);
})->middleware(['auth'])->name('dashboard');

// Route::get('/dashboard/modules/{type?}', [Admin\PanelController::class, 'index'])
//     ->middleware(['auth', 'check_subscription'])
//     ->name('dashboard.panel');

Route::get('/dashboard/modules/{type?}', [Admin\PanelController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard.panel');

Route::get('/module-tags', [Admin\PanelController::class, 'moduleTags'])
    ->middleware(['auth'])
    ->name('module-tags');

Route::middleware(['auth', 'ValidateRoleStatus'])
    ->as('dashboard.')
    ->prefix('dashboard')
    ->group(function () {

        // management of administrators operations
        Route::group(['middleware' => ['can:view_administrators']], function () {
            Route::resource('administrators', Admin\Roles\AdministratorController::class);
            Route::get('administrator/change/status/{id}', [Admin\Roles\AdministratorController::class, 'changeStatus'])
                ->name('administrator.change.status');
        });

        // management of business owners operations
        Route::group(['middleware' => ['can:view_business_owners']], function () {
            Route::resource('business/owners', Admin\Roles\BusinessOwnerController::class);
            Route::get('owner/change/status/{id}', [Admin\Roles\BusinessOwnerController::class, 'changeStatus'])->name('owner.change.status');
        });

        // management of profile
        Route::resource('profile', Admin\ProfileController::class);
        Route::post('change-password', [Admin\ProfileController::class, 'changePassword'])
            ->name('profile.change.password');
        Route::post('change-avatar', [Admin\ProfileController::class, 'updateAvatar'])
            ->name('profile.change.avatar');
        Route::post('edit/address', [Admin\ProfileController::class, 'updateAddress'])
            ->name('profile.update.address');


        // management of customers operations
        Route::group(['middleware' => ['can:view_customers']], function () {
            Route::resource('customers', Admin\Roles\CustomerController::class);
            Route::get('customer/change/status/{id}', [Admin\Roles\CustomerController::class, 'changeStatus'])->name('customer.change.status');
            Route::post('customer/change/role/{id}', [Admin\Roles\CustomerController::class, 'changeRole'])->name('customer.change.role');

        });

        // managemnet of reporters operation
        Route::group(['middleware' => ['can:view_reporters']], function () {
            Route::resource('reporters', Admin\Roles\ReporterController::class);
            Route::get('reporter/change/status/{id}', [Admin\Roles\ReporterController::class, 'changeStatus'])->name('reporter.change.status');
        });

        // management of remote assistance
        Route::group(['middleware' => ['can:view_remote_assistants']], function () {
            Route::resource('remote/assistants', Admin\Roles\RemoteAssistantController::class);
            Route::get('assistant/change/status/{id}', [Admin\Roles\RemoteAssistantController::class, 'changeStatus'])
                ->name('assistant.change.status');
        });


        //management of news
        Route::group(['middleware' => ['can:view_news']], function () {
            Route::resource('/news/categories', Admin\NewsCategoriesController::class);
        });

        //management of news categories
        Route::group(['middleware' => ['can:view_news']], function () {
            Route::resource('/news', Admin\NewsController::class);
        });

        /*
        * Settings
        */
        Route::group(['as' => 'settings.', 'middleware' => ['can:view_settings']], function () {
            // Language Management
            Route::group(['middleware' => ['can:view_languages']], function () {
                Route::resource('languages', Admin\Settings\LanguageController::class);
                Route::get('language/change/status/{id}', [Admin\Settings\LanguageController::class, 'changeStatus'])
                    ->name('language.change.status');
            });
            // Roles
            Route::group(['middleware' => ['can:view_roles']], function () {
                Route::resource('roles', Admin\Settings\RoleController::class);
            });
            // General Settings
            Route::resource('generals', Admin\Settings\SettingsController::class);
            Route::post('general/update', [Admin\Settings\SettingsController::class, 'updateSettings'])->name('general.update');

            // email and  push notifications
            Route::get('settings/{group}', [Admin\Settings\SettingsController::class, 'groupSetting'])->name('general.group');
            Route::get('settings/{group}/{type}', [Admin\Settings\SettingsController::class, 'groupType'])->name('group.type');
            Route::post('general/group/type/values', [Admin\Settings\SettingsController::class, 'changeTypeValues'])->name('general.group.type.values');

            // checkout fields
            Route::post('general/checkout/{id}/{type}', [Admin\Settings\SettingsController::class, 'updateCheckoutSettings'])->name('general.checkout');

            // news paper delivery zone
            Route::group(['middleware' => ['can:view_delivery_zones']], function () {
                Route::resource('deliveyzone', Admin\Settings\DeliveryZoneController::class);
                Route::get('deliveryzone/change/status/{id}', [Admin\Settings\DeliveryZoneController::class, 'changeStatus'])->name('deliveryzone.status');
            });

            // chat settings
            Route::group(['middleware' => ['can:view_chat_settings']], function () {
                Route::get('chat-settings', [Admin\Settings\ChatSettingsController::class, 'index'])->name('chat-settings.index');
                Route::post('update-chat-settings', [Admin\Settings\ChatSettingsController::class, 'updateChatSettings'])->name('update-chat-setting');
            });
        });

        /*
        * Drivers
        */
        Route::group(['middleware' => ['can:view_drivers']], function () {
            Route::resource('/drivers', Admin\Drivers\DriverController::class);
            Route::get('driver/change/status/{id}', [Admin\Drivers\DriverController::class, 'changeStatus'])->name('driver.status');
        });

        Route::group(['as' => 'driver.'], function () {
            // Drivers Manager
            Route::group(['middleware' => ['can:view_drivers_manager']], function () {
                Route::resource('managers', Admin\Drivers\ManagerController::class);
                Route::get('manager/change/status/{id}', [Admin\Drivers\ManagerController::class, 'changeStatus'])
                    ->name('manager.status');
            });

            // Drivers group
            Route::group(['middleware' => ['can:view_drivers_group']], function () {
                Route::resource('groups', Admin\Drivers\GroupController::class);
                Route::get('group/change/status/{id}', [Admin\Drivers\GroupController::class, 'changeStatus'])->name('group.status');
            });
        });

        //Payment Method
        Route::group(['prefix' => 'subscription', 'as' => 'subscription.'], function () {
            Route::resource('/payment-method', Admin\Subscription\CreditCardController::class)->middleware('can:view_payment_method');
            Route::resource('/subscribe', Admin\Subscription\SubscribeController::class)->middleware(['can:view_subscription']);
            Route::post('/subscribe/get-plan', [Admin\Subscription\SubscribeController::class, 'getPlan'])->name('subscribe.getPlan')->middleware('can:view_subscription');
            Route::post('/subscribe/pay', [Admin\Subscription\SubscribeController::class, 'payLastInvoice'])->name('subscribe.pay')->middleware('can:view_subscription');
            Route::post('subscribe/checkActiveBusinesses', [Admin\Subscription\SubscribeController::class, 'checkActiveBusinesses'])->name('subscribe.checkActiveBusinesses');
        });

        /*
        * Business
        */
        Route::get('business/list/{moduleId}', [Admin\Business\BusinessController::class, 'getBusinessList'])->name('business.options');
        Route::group(['middleware' => ['can:view_business']], function () {
            Route::get('business/change/status/{id}', [Admin\Business\BusinessController::class, 'changeStatus'])->name('business.status');
            Route::post('/business/images', [Admin\Business\BusinessController::class, 'storeImages'])->name('business.image');
            // Route::get('modules/{moduleId?}/businesses', [Admin\Business\BusinessController::class, 'index'])->name('business.index');
            Route::resource('businesses', App\Http\Controllers\Admin\Business\BusinessController::class);
            Route::delete('/business/images/delete/{id}', [Admin\Business\BusinessController::class, 'deleteImages'])->name('business.image.delete');
            Route::post('assign/module/tags', [Admin\Business\BusinessController::class, 'moduleTags'])->name('assign-module-tags');
        });

        /*
        * Business Settings
        */
        Route::group(['prefix' => 'business/{business_uuid}', 'as' => 'business.'], function () {
            // general settings
            Route::group(['middleware' => ['can:view_business_settings']], function () {
                Route::resource('/settings', Admin\Business\SettingsController::class);
            });

            // Business Reviews
            Route::group(['middleware' => ['can:view_reviews']], function () {
                Route::resource('/reviews', Admin\Business\ReviewController::class);
                Route::get('review/status/{id}', [Admin\Business\ReviewController::class, 'changeStatus'])->name('review.status');
            });
        });

        // business admin settings
        // Route::group(['prefix' => 'modules/{track_id}/business/{business_uuid}', 'as' => 'modules.business.'], function () {
        //     Route::group(['middleware' => ['can:view_admin_settings']], function () {
        //         Route::get('/admin-setting',  [Admin\Business\SecontrollerttingsController::class, 'standardTagsAdminSettings'])->name('admin-settings');
        //         Route::get('assign/standard-tags/{tags}', [Admin\Business\SettingsController::class, 'assignTags'])
        //             ->name('assign.standard-tags');
        //         Route::get('admin/settings', [Admin\Business\SettingsController::class, 'adminSettings'])->name('admin.settings');
        //         Route::post('settings/update', [Admin\Business\SettingsController::class, 'updateSettings'])
        //             ->name('platform-fee-type.update');
        //         Route::get('admin/assign-fee-type', [Admin\Business\SettingsController::class, 'assignFeeType'])->name('admin.assignFeeType');
        //         Route::post('assign-fee-type/update', [Admin\Business\SettingsController::class, 'updateFeeType'])
        //             ->name('feeType.update');
        //         Route::post('/filter-industry-tags',  [Admin\Business\SettingsController::class, 'filterIndustryTags'])->name('industryTags');
        //     });
        // });

        /*
        * media
        */
        Route::get('remove/media/{id}/{type}/{businessId?}/{responseType?}', [Admin\MediaController::class, 'deleteMedia'])->name('media.remove');
        Route::post('change/media/{id}/{type}', [Admin\MediaController::class, 'changeMedia'])->name('media.change');
        Route::post('business/optional/images/{id}', [Admin\MediaController::class, 'businessOptionalMedia'])->name('business.optional.images');
        Route::post('business/optional/images/remove/{uuid}/{id}', [Admin\MediaController::class, 'deleteBusinessOptionalMedia'])->name('business.optional.images.remove');

        /*
        * Route for StandardProductTag
        */
        Route::group(['middleware' => ['can:view_standard_tag']], function () {
            Route::resource('productTag', Admin\StandardTags\ProductTagController::class);
            Route::get('productTag/change/status/{id}', [Admin\StandardTags\ProductTagController::class, 'changeStatus'])->name('productTag.status');
        });

        /*
        * Route for StandardBrandTag
        */
        Route::group(['middleware' => ['can:view_standard_tag']], function () {
            Route::resource('brandTag', Admin\StandardTags\BrandTagController::class);
            Route::get('brandTag/change/status/{id}', [Admin\StandardTags\BrandTagController::class, 'changeStatus'])->name('brandTag.status');
        });

        /*
        * Route for StandardAttributeTag
        */
        Route::group(['middleware' => ['can:view_standard_tag']], function () {
            Route::resource('/{slug}/attributeTag', Admin\StandardTags\AttributeTagController::class);
            Route::get('attributeTag/change/status/{id}', [Admin\StandardTags\AttributeTagController::class, 'changeStatus'])->name('attributeTag.status');
            Route::post('attributeTag/position', [Admin\StandardTags\AttributeTagController::class, 'setPosition'])->name('attributeTag.position');
        });

        // Attribute Crud
        Route::group(['middleware' => ['can:view_attributes']], function () {
            Route::resource('attributes', Admin\AttributeController::class);
            Route::get('attributes/change/status/{id}', [Admin\AttributeController::class, 'changeStatus'])->name('attributes.status');
        });

        Route::group(['middleware' => ['can:view_tags_mapper']], function () {
            Route::resource('/tag-mappers', Admin\Mapping\OrphanTagMapperController::class);
            Route::get('/tag-mappers/{type}', [Admin\Mapping\OrphanTagMapperController::class, 'getStandardTag'])->name('tag-mappers.tags');
            Route::get('tag-mappers/extra/tgas', [Admin\Mapping\OrphanTagMapperController::class, 'makeExtraTags'])->name('tag-mappers.extra-tag');
            Route::post('tag-mappers/clone/{id}', [Admin\Mapping\OrphanTagMapperController::class, 'cloneTag'])->name('tag-mappers.clone-tag');
            Route::post('filter-standard-tags-list', [Admin\Mapping\OrphanTagMapperController::class, 'filterStandardTagsList'])->name('filterStandardTagsList');
        });


        // industry tags routes
        Route::group(['middleware' => ['can:view_industry_tag']], function () {
            Route::resource('/tag', Admin\StandardTags\IndustryTagController::class);
            Route::get('tag/change/status/{id}', [Admin\StandardTags\IndustryTagController::class, 'changeStatus'])->name('tag.status');
        });

        Route::group(['middleware' => ['can:view_tags_mapper']], function () {
            Route::resource('/standard-tag-mapper', Admin\Mapping\StandardTagMapperController::class);
            Route::get('/get-tags', [Admin\Mapping\StandardTagMapperController::class, 'getTags'])->name('get-tags');
            Route::get('search/standardTags', [Admin\Mapping\StandardTagMapperController::class, 'searchStandardTags'])->name('search.standardTags');
        });

        // news paper logo
        Route::group(['middleware' => ['can:view_settings']], function () {
            Route::get('news/paper/logo/', [Admin\Settings\SettingsController::class, 'getNewsPaperLogo'])->name('news-paper.logo');
            Route::post('new/paper/logo/update/{id}', [Admin\Settings\SettingsController::class, 'updateNewsPaperLog'])->name('news-paper.logo.update');
        });
    });

Route::get('/order-truncate', function () {
    $toTruncate = [
        'orders',
        'order_items'
    ];

    Schema::disableForeignKeyConstraints();
    foreach ($toTruncate as $table) {
        DB::table($table)->truncate();
    }
    Schema::enableForeignKeyConstraints();
});

Route::get('/job-truncate', function () {
    $toTruncate = [
        'jobs',
        'failed_jobs'
    ];

    Schema::disableForeignKeyConstraints();
    foreach ($toTruncate as $table) {
        DB::table($table)->truncate();
    }
    Schema::enableForeignKeyConstraints();
});

Route::get('/remove-business', function () {
    $toTruncate = [
        'businesses',
        'business_schedules',
        'business_settings',
        'business_tag'
    ];

    Schema::disableForeignKeyConstraints();
    foreach ($toTruncate as $table) {
        DB::table($table)->truncate();
    }
    Schema::enableForeignKeyConstraints();
});

Route::get('/send-notification', [App\Http\Controllers\Admin\PanelController::class, 'sendNotification'])->name('send.notification');

Route::get('/send/app/notification', [Admin\PanelController::class, 'appNotificationsTesting']);

Route::get('save-device-token', [App\Http\Controllers\Admin\Settings\SettingsController::class, 'saveDeviceToken'])->name('save-device-token');

require __DIR__ . '/auth.php';
