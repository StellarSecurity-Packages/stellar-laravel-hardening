<?php

namespace StellarSecurity\LaravelHardening;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DebugGuardServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $debug = Config::get('app.debug');

        // Normalize debug to boolean, handles string values like "true" / "1"
        $debugBool = filter_var($debug, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

        // Safety: never allow debug mode in production-like environments
        if ($this->isProdLikeEnv() && $debugBool === true) {
            Log::critical('APP_DEBUG is enabled in a production-like environment. Refusing to boot.', [
                'env'   => App::environment(),
                'value' => $debug,
            ]);

            // Fail hard so the app never exposes debug pages
            abort(500, 'Application misconfigured.');
        }
    }

    /**
     * Decide which environments are considered "production-like".
     * Adjust this list as needed (for example: add "staging" or "preprod").
     */
    protected function isProdLikeEnv(): bool
    {
        return App::environment('production');
    }
}
