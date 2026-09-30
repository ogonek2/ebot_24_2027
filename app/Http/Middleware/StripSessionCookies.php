<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Safety net: strip any Set-Cookie on /api/lead-log if a session middleware
 * somehow still ran. Primary fix is registering lead-log without Sanctum.
 */
class StripSessionCookies
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!$request->is('api/lead-log')) {
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
