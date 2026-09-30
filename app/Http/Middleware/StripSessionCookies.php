<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Lead-log is a fire-and-forget beacon (credentials: omit).
 * Sanctum still starts a session for stateful Origins and queues Set-Cookie,
 * which Firefox/Chrome reject as cross-site SameSite=Lax — noisy false alarms.
 *
 * Must run as an OUTER middleware so it strips cookies AFTER AddQueuedCookiesToResponse.
 */
class StripSessionCookies
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!$request->is('api/lead-log')) {
            return $response;
        }

        if (method_exists($response, 'headers')) {
            $response->headers->remove('Set-Cookie');
        }

        try {
            foreach (array_keys(app('cookie')->getQueuedCookies()) as $name) {
                app('cookie')->unqueue($name);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return $response;
    }
}
