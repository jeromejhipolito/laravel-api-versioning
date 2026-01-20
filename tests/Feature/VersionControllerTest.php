<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use JeromeJHipolito\ApiVersioning\Http\Controllers\VersionController;
use JeromeJHipolito\ApiVersioning\Middleware\ApiVersionMiddleware;

beforeEach(function () {
    config(['api-versioning.supported_versions' => ['1.0.0']]);
    config(['api-versioning.enabled_versions' => null]);
    config(['api-versioning.default_version' => '1.0.0']);

    Route::middleware(ApiVersionMiddleware::class)
        ->get('/api/version/status', [VersionController::class, 'status']);
});

it('returns version status with all version information', function () {
    config(['api-versioning.supported_versions' => ['1.0.0', '1.1.0', '2.0.0']]);
    config(['api-versioning.enabled_versions' => ['1.0.0', '1.1.0']]);

    $response = $this->withHeader('X-API-Version', '1.0.0')
        ->getJson('/api/version/status');

    $response->assertStatus(200);
    $response->assertJson([
        'status' => 'success',
        'data'   => [
            'current_version'    => '1.0.0',
            'version_enabled'    => true,
            'default_version'    => '1.0.0',
            'supported_versions' => ['1.0.0', '1.1.0', '2.0.0'],
            'enabled_versions'   => ['1.0.0', '1.1.0'],
            'version_flags'      => [
                '1.0.0' => true,
                '1.1.0' => true,
                '2.0.0' => false,
            ],
        ],
    ]);
});

it('returns all versions as enabled when enabled_versions is null', function () {
    config(['api-versioning.supported_versions' => ['1.0.0', '1.1.0', '2.0.0']]);
    config(['api-versioning.enabled_versions' => null]);

    $response = $this->withHeader('X-API-Version', '1.0.0')
        ->getJson('/api/version/status');

    $response->assertStatus(200);
    $response->assertJson([
        'status' => 'success',
        'data'   => [
            'version_flags' => [
                '1.0.0' => true,
                '1.1.0' => true,
                '2.0.0' => true,
            ],
        ],
    ]);
});
