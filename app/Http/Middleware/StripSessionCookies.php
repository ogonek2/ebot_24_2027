<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Safety net: strip Set-Cookie on cookie-free public endpoints if session
 * middleware somehow still ran (cross-site SameSite console noise).
 */
class StripSessionCookies
{
    /** @var list<string> */
    private const PATHS = [
        'api/lead-log',
        'api/order/submit',
        'api/order/last',
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!$request->is(...self::PATHS)) {
            return $response;
        }

        try {
            foreach (array_keys(app('cookie')->getQueuedCookies()) as $name) {
                app('cookie')->unqueue($name);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        if (!method_exists($response, 'headers')) {
            return $response;
        }

        // Symfony stores cookies separately from the raw Set-Cookie header list
        try {
            foreach ($response->headers->getCookies() as $cookie) {
                $response->headers->removeCookie(
                    $cookie->getName(),
                    $cookie->getPath(),
                    $cookie->getDomain()
                );
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $response->headers->remove('Set-Cookie');

        return $response;
    }
}
