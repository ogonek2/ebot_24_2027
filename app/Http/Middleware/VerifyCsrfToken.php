<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Public lead + checkout must not depend on SPA session/CSRF cookies
     * (frontend enot-24.com.ua → API enot-api.* is cross-site).
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/contact',
        'api/order/consultation',
        'api/order/submit',
        'api/courier/request',
        'api/b2b/proposal',
        'api/lead-log',
    ];
}
