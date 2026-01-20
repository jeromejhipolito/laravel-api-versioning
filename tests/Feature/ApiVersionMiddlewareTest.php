<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use JeromeJHipolito\ApiVersioning\Middleware\ApiVersionMiddleware;

beforeEach(function () {
    config(['api-versioning.supported_versions' => ['1.0.0']]);
    config(['api-versioning.enabled_versions' => null]);
    config(['api-versioning.default_version' => '1.0.0']);

    Route::middleware(ApiVersionMiddleware::class)->get('/test', function () {
        return response()->json(['message' => 'ok']);
    });
});

it('accepts supported and enabled API version', function () {
    $response = $this->withHeader('X-API-Version', '1.0.0')
        ->getJson('/test');

    $response->assertStatus(200);
    $response->assertHeader('X-API-Version', '1.0.0');
    $response->assertHeader('X-API-Version-Enabled', 'true');
});

it('rejects unsupported API version', function () {
    $response = $this->withHeader('X-API-Version', '99.0.0')
        ->getJson('/test');

    $response->assertStatus(400);
    $response->assertJson(['status' => 'error']);
});

it('returns disabled response when version is supported but not enabled', function () {
    config(['api-versioning.supported_versions' => ['1.0.0', '2.0.0']]);
    config(['api-versioning.enabled_versions' => ['1.0.0']]);

    $response = $this->withHeader('X-API-Version', '2.0.0')
        ->getJson('/test');

    $response->assertStatus(200);
    $response->assertJson([
        'status'          => 'success',
        'version_enabled' => false,
        'current_version' => '2.0.0',
        'data'            => null,
    ]);
});

it('defaults to latest version when header is missing', function () {
    $response = $this->getJson('/test');

    $response->assertHeader('X-API-Version', '1.0.0');
    $response->assertHeader('X-API-Version-Enabled', 'true');
});

it('enables all supported versions when enabled_versions config is null', function () {
    config(['api-versioning.supported_versions' => ['1.0.0', '1.1.0', '2.0.0']]);
    config(['api-versioning.enabled_versions' => null]);

    $response = $this->withHeader('X-API-Version', '2.0.0')
        ->getJson('/test');

    $response->assertHeader('X-API-Version-Enabled', 'true');
    $response->assertStatus(200);
});

it('rejects invalid API version format', function () {
    $response = $this->withHeader('X-API-Version', '1.0')
        ->getJson('/test');

    $response->assertStatus(400);
    $response->assertJson(['status' => 'error']);
});

it('rejects empty API version string', function () {
    $response = $this->withHeader('X-API-Version', '')
        ->getJson('/test');

    $response->assertStatus(400);
});
