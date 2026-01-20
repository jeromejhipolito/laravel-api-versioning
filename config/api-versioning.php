<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Supported API Versions
    |--------------------------------------------------------------------------
    |
    | List all API versions that your application supports. These versions
    | will be validated against incoming requests. Use semantic versioning
    | format: major.minor.patch (e.g., '1.0.0', '1.1.0', '2.0.0')
    |
    */
    'supported_versions' => ['1.0.0'],

    /*
    |--------------------------------------------------------------------------
    | Enabled API Versions
    |--------------------------------------------------------------------------
    |
    | List of versions that are currently enabled. This acts as a feature flag
    | system - versions can be supported but not yet enabled (useful during
    | app store review periods). Set to null to enable all supported versions.
    |
    | You can set this via environment variable as a comma-separated list:
    | API_ENABLED_VERSIONS=1.0.0,1.1.0
    |
    */
    'enabled_versions' => env('API_ENABLED_VERSIONS')
        ? array_map('trim', explode(',', env('API_ENABLED_VERSIONS')))
        : null,

    /*
    |--------------------------------------------------------------------------
    | Default API Version
    |--------------------------------------------------------------------------
    |
    | The version to use when no X-API-Version header is provided in the
    | request. This should typically be your latest stable version.
    |
    */
    'default_version' => env('API_DEFAULT_VERSION', '1.0.0'),

    /*
    |--------------------------------------------------------------------------
    | Register Status Route
    |--------------------------------------------------------------------------
    |
    | Whether to automatically register the /api/version/status route.
    | Set to false if you want to define your own route.
    |
    */
    'register_status_route' => true,

    /*
    |--------------------------------------------------------------------------
    | Status Route Path
    |--------------------------------------------------------------------------
    |
    | The path for the version status endpoint.
    |
    */
    'status_route_path' => 'api/version/status',

    /*
    |--------------------------------------------------------------------------
    | Versioned Controllers
    |--------------------------------------------------------------------------
    |
    | Map of version-specific controller overrides. When a request comes in
    | for a specific major version, the middleware will swap the controller
    | to the versioned one if defined here.
    |
    | Format:
    | 'versioned_controllers' => [
    |     2 => [
    |         'App\Http\Controllers\Api\V1\UserController' => 'App\Http\Controllers\Api\V2\UserController',
    |     ],
    | ],
    |
    */
    'versioned_controllers' => [],
];
