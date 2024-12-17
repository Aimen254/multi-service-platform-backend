<?php

namespace App\Http\Middleware;

use App\Enums\Business\Settings\DeliveryType;
use Inertia\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user['role'] = getRole($request->user());
            $user['permissions'] = getPermissionsName(\getRole($request->user()));
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
            ],
            'current_route' => Route::current(),
            'flash' => session()->get('flash'),
            'file_url' => env('FILE_URL'),
        ]);
    }
}
