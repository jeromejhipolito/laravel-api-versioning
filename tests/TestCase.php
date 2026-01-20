<?php

declare(strict_types=1);

namespace JeromeJHipolito\ApiVersioning\Tests;

use JeromeJHipolito\ApiVersioning\ApiVersioningServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ApiVersioningServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('api-versioning.supported_versions', ['1.0.0']);
        $app['config']->set('api-versioning.enabled_versions', null);
        $app['config']->set('api-versioning.default_version', '1.0.0');
    }
}
