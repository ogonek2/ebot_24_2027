<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Cross-origin SPA (localhost / enot.* → enot-api.*) needs SameSite=None
        // or browsers reject XSRF/session cookies entirely.
        $sameSite = strtolower((string) config('session.same_site', 'none'));
        if ($sameSite === '' || $sameSite === 'lax' || $sameSite === 'strict') {
            $frontend = (string) env('FRONTEND_URL', '');
            $appUrl = (string) env('APP_URL', '');
            $frontendHost = $frontend !== '' ? parse_url($frontend, PHP_URL_HOST) : null;
            $appHost = $appUrl !== '' ? parse_url($appUrl, PHP_URL_HOST) : null;

            if ($frontendHost && $appHost && $frontendHost !== $appHost) {
                config([
                    'session.same_site' => 'none',
                    'session.secure' => true,
                ]);
            }
        }

        if (config('session.same_site') === 'none') {
            config(['session.secure' => true]);
        }
    }
}
