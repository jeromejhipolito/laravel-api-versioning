<?php

declare(strict_types=1);

namespace JeromeJHipolito\ApiVersioning;

use Illuminate\Support\ServiceProvider;
use JeromeJHipolito\ApiVersioning\Middleware\ApiVersionMiddleware;
use JeromeJHipolito\ApiVersioning\Middleware\ResolveVersionedController;

class ApiVersioningServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/api-versioning.php', 'api-versioning');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/api-versioning.php' => config_path('api-versioning.php'),
        ], 'api-versioning-config');

        $this->publishes([
            __DIR__.'/../resources/lang' => lang_path(),
        ], 'api-versioning-lang');

        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');

        if (config('api-versioning.register_status_route', true)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        }
    }

    public function provides(): array
    {
        return [
            ApiVersionMiddleware::class,
            ResolveVersionedController::class,
        ];
    }
}
