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
        /*
         | SPA (enot-24.com.ua / enot.qpanel-erp.online) and API (enot-api.*)
         | are cross-site. Browsers reject session/XSRF cookies unless:
         |   SameSite=None; Secure
         |
         | Default ON. Set SESSION_FORCE_CROSS_SITE=false only if SPA+API
         | are truly same-origin.
         */
        $forceCrossSite = filter_var(
            env('SESSION_FORCE_CROSS_SITE', true),
            FILTER_VALIDATE_BOOLEAN
        );

        if ($forceCrossSite) {
            config([
                'session.same_site' => 'none',
                'session.secure' => true,
            ]);
            return;
        }

        // Fallback: auto-detect mismatched FRONTEND_URL vs APP_URL
        $sameSite = strtolower((string) config('session.same_site', 'none'));
        if ($sameSite === '' || $sameSite === 'lax' || $sameSite === 'strict') {
            $frontendHost = $this->hostFromEnv('FRONTEND_URL');
            $appHost = $this->hostFromEnv('APP_URL');
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

    private function hostFromEnv(string $key): ?string
    {
        $url = (string) env($key, '');
        if ($url === '') {
            return null;
        }
        $host = parse_url($url, PHP_URL_HOST);
        return is_string($host) && $host !== '' ? strtolower($host) : null;
    }
}
