<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
        /**
         * The URIs that should be excluded from CSRF verification.
         *
         * @var array
         */
        protected $except = [
                'http://localhost:8000/stripe-webhooks',
                'stripe-webhooks',
                'automotive/dashboard/*/vehicle/*/media',
                'retail/dashboard/*/product/*/media',
                'news/dashboard/*/news/*/media',
                'obituaries/dashboard/*/obituaries/*/media',
                'recipes/dashboard/*/recipes/*/media',
                'blogs/dashboard/*/blogs/*/media',
                'classifieds/dashboard/*/classifieds/*/media',
                'taskers/dashboard/*/taskers/*/media',
                'boats/dashboard/*/boat/*/media',
                'employment/dashboard/*/post/*/media',
                'services/dashboard/*/services/*/media',
                'government/dashboard/*/post/*/media',
                'notices/dashboard/*/notices/*/media',
                'real-estate/dashboard/*/properties/*/media',
                'events/dashboard/*/events/*/media',

        ];
}
