<?php

namespace Modules\News\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CanAccessNewsPlans
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!(auth()->user()->hasRole('admin') || auth()->user()->hasRole('reporter')) && $request?->moduleSlug === 'news') {
            return back();
        } else {
            return $next($request);
        }
    }
}
