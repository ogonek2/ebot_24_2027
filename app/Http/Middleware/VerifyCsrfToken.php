<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Public lead forms must not depend on SPA session/CSRF cookies.
     * A mismatched token previously returned 419 while the frontend still
     * showed "sent" — lost consultation leads for days.
     *
     * Cart / order submit stay protected (session required).
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/contact',
        'api/order/consultation',
        'api/courier/request',
        'api/b2b/proposal',
        'api/lead-log',
    ];
}
