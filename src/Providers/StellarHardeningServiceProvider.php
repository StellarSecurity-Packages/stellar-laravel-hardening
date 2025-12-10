<?php

namespace Stellar\LaravelHardening\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Stellar\LaravelHardening\Http\Middleware\ProductionHardeningMiddleware;

class StellarHardeningServiceProvider extends ServiceProvider
{
    /**
     * Register bindings.
     */
    public function register(): void
    {
        // Merge default config
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/stellar_hardening.php',
            'stellar_hardening'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(Router $router): void
    {
        // Allow apps to publish the config file
        $this->publishes([
            __DIR__ . '/../../config/stellar_hardening.php' => config_path('stellar_hardening.php'),
        ], 'config');

        // Attach middleware to both web and api groups
        $router->pushMiddlewareToGroup('web', ProductionHardeningMiddleware::class);
        $router->pushMiddlewareToGroup('api', ProductionHardeningMiddleware::class);
    }
}
