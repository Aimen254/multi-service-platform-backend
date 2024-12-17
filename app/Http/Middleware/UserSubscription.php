<?php

namespace App\Http\Middleware;

use Closure;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use App\Traits\StripeSubscription;
use Illuminate\Support\Facades\Route;

class UserSubscription
{
    use StripeSubscription;
    protected StripeClient $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $module = null)
    {
        if ($request->user() && ($request->user()->user_type == 'business_owner' || $request->user()->user_type == 'customer')) {
            $tags = StandardTag::whereType('module')->active()->pluck('id');
            $parameter = Route::current()->parameters['type'];
            if ($tags->contains($parameter)) {
                $allowedModules = $this->checkAllowedModules();
                $moduleTags = StandardTag::whereType('module')->active()->when(request()->user()->user_type == 'business_owner' || request()->user()->user_type == 'customer', function ($query) use ($allowedModules) {
                    $query->whereIn('slug', $allowedModules);
                })->pluck('id');
                if (!$moduleTags->contains($parameter)) {
                    abort(404);
                }
            }
        }
        return $next($request);
    }
}
