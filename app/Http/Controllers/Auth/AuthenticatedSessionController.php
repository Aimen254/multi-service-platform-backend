<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {


        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();
        if ($user && $request->input('device_token')) {
            $user->devices()->updateOrCreate([
                'device_token' => $request->device_token,
            ], [
                'device_type' => $request->device_type,
                'device_name' => $request->device_name,
                'language' => $request->language,
                'send_notification' => $request->notification
            ]);
        }
        // if($user->hasRole('customer')){
        //     $this->destroy($request);
        //     throw ValidationException::withMessages([
        //         'email' => __('auth.failed'),
        //     ]);
        // }
        return \redirect()->route('dashboard.panel', 'settings');

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $user = $request->user();
        $user->devices()->delete();
        Auth::guard('web')->logout();
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
