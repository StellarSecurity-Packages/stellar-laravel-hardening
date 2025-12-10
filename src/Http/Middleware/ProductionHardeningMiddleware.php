<?php

namespace Stellar\LaravelHardening\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductionHardeningMiddleware
{
    /**
     * Guard against debug / dev settings leaking into production-like environments.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $config = config('stellar_hardening', []);

        $appEnv = (string) config('app.env', env('APP_ENV', 'production'));
        $debug  = (bool) config('app.debug', env('APP_DEBUG', false));

        $host     = (string) $request->getHost();
        $siteName = (string) env('WEBSITE_SITE_NAME', '');

        $isProdLike = false;

        // 1. Explicit production environments
        $prodEnvs = $config['production_envs'] ?? ['production', 'prod'];
        if (in_array($appEnv, $prodEnvs, true)) {
            $isProdLike = true;
        }

        // 2. Hostname checks
        $hostContains = $config['host_contains'] ?? [];
        foreach ($hostContains as $needle) {
            $needle = (string) $needle;
            if ($needle !== '' && str_contains($host, $needle)) {
                $isProdLike = true;
                break;
            }
        }

        // 3. WEBSITE_SITE_NAME checks (Azure)
        $siteNameContains = $config['site_name_contains'] ?? [];
        foreach ($siteNameContains as $needle) {
            $needle = (string) $needle;
            if ($needle !== '' && str_contains($siteName, $needle)) {
                $isProdLike = true;
                break;
            }
        }

        if ($isProdLike && $debug) {
            Log::critical('APP_DEBUG is enabled in a production-like environment', [
                'app_env'    => $appEnv,
                'host'       => $host,
                'site_name'  => $siteName,
            ]);

            abort(500, $config['abort_message'] ?? 'Stellar hardening: Misconfigured debug in production.');
        }

        return $next($request);
    }
}
