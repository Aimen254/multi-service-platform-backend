<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResponseTimeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (env('APP_ENV') == 'local') {
            $start = microtime(true);

            $response = $next($request);

            $end = microtime(true);

            $responseTime = round(($end - $start) * 1000, 2); // Response time in milliseconds

            // Log or store $responseTime as needed
            Log::channel('api-response-times')
                ->info("API Response Time for endpoint => {$request->url()} is: {$responseTime}ms");
            return $response;
        }
        return $next($request);
    }
}
