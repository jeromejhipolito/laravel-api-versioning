<?php

declare(strict_types=1);

namespace JeromeJHipolito\ApiVersioning\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VersionController extends Controller
{
    public function status(Request $request): JsonResponse
    {
        $supportedVersions = config('api-versioning.supported_versions', ['1.0.0']);
        $enabledVersions = config('api-versioning.enabled_versions') ?? $supportedVersions;
        $defaultVersion = config('api-versioning.default_version', '1.0.0');
        $currentVersion = $request->attributes->get('api_version', $defaultVersion);
        $isEnabled = $request->attributes->get('api_version_enabled', true);

        $versionFlags = [];
        foreach ($supportedVersions as $version) {
            $versionFlags[$version] = in_array($version, $enabledVersions, true);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'current_version' => $currentVersion,
                'version_enabled' => $isEnabled,
                'default_version' => $defaultVersion,
                'supported_versions' => $supportedVersions,
                'enabled_versions' => $enabledVersions,
                'version_flags' => $versionFlags,
            ],
        ]);
    }
}
