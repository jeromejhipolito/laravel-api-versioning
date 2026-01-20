<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use JeromeJHipolito\ApiVersioning\Http\Controllers\VersionController;

Route::get(config('api-versioning.status_route_path', 'api/version/status'), [VersionController::class, 'status'])
    ->name('api.version.status');
