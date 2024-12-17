<?php

namespace App\Providers;

use App\Models\Setting;
use Stripe\StripeClient;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // custom validation to accept data without spaces
        Validator::extend('without_spaces', function ($attribute, $value) {
            return preg_match('/^\S*$/u', $value);
        });
        if (Schema::hasTable('settings')) {
            $clientSecret = Setting::where('key', 'sandbox')->first();
            if ($clientSecret) {
                $this->app->singleton(StripeClient::class, function () {
                    //Getting Stripe Id
                    $clientSecret = Setting::where('key', 'sandbox')->first()->value == 'no'
                        ?  Setting::where('key', 'client_secret_production')->first()
                        : Setting::where('key', 'client_secret_sandbox')->first();
                    return new StripeClient($clientSecret->value);
                });
            }
        }

        // Defining pagination on simple collection object

        if (!Collection::hasMacro('paginate')) {
            Collection::macro(
                'paginate',
                function ($perPage = 15, $page = null, $options = []) {
                    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
                    return (new LengthAwarePaginator(
                        $this->forPage($page, $perPage),
                        $this->count(),
                        $perPage,
                        $page,
                        $options
                    ))
                        ->withPath('');
                }
            );
        }
    }
}
