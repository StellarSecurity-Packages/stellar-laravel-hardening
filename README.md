# stellar-laravel-hardening

Tiny Laravel package that protects you from pushing `APP_DEBUG=true` to anything that *smells* like production.

## What it does

- Looks at:
  - `APP_ENV` / `config('app.env')`
  - `APP_DEBUG` / `config('app.debug')`
  - Current HTTP host
  - `WEBSITE_SITE_NAME` (Azure App Service)
- If the environment is "production-like" and debug is enabled, it:
  - Logs a critical message
  - Aborts with HTTP 500

## Install

```bash
composer require stellar/laravel-hardening
```

The service provider is auto-discovered.

Publish the config if you want to tweak the rules:

```bash
php artisan vendor:publish --tag=config --provider="Stellar\LaravelHardening\Providers\StellarHardeningServiceProvider"
```

Then edit `config/stellar_hardening.php`.
