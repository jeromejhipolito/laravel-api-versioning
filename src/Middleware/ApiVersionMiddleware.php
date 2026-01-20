<?php

declare(strict_types=1);

namespace JeromeJHipolito\ApiVersioning\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiVersionMiddleware
{
    private const string HEADER_NAME = 'X-API-Version';

    private const string VERSION_PATTERN = '/^\d+\.\d+\.\d+$/';

    public function handle(Request $request, Closure $next): Response
    {
        $supportedVersions = config('api-versioning.supported_versions', ['1.0.0']);
        $enabledVersions = config('api-versioning.enabled_versions') ?? $supportedVersions;
        $defaultVersion = config('api-versioning.default_version', '1.0.0');

        $version = $request->header(self::HEADER_NAME, $defaultVersion);

        if (! $this->isValidVersionFormat($version)) {
            return $this->unsupportedVersionResponse($supportedVersions, $enabledVersions);
        }

        if (! in_array($version, $supportedVersions, true)) {
            return $this->unsupportedVersionResponse($supportedVersions, $enabledVersions);
        }

        $versionParts = $this->parseVersion($version);
        $isEnabled = in_array($version, $enabledVersions, true);

        $request->attributes->set('api_version', $version);
        $request->attributes->set('api_major_version', $versionParts['major']);
        $request->attributes->set('api_minor_version', $versionParts['minor']);
        $request->attributes->set('api_patch_version', $versionParts['patch']);
        $request->attributes->set('api_version_enabled', $isEnabled);

        if (! $isEnabled) {
            return $this->disabledVersionResponse($version, $supportedVersions, $enabledVersions);
        }

        $response = $next($request);

        $this->addVersionHeaders($response, $version, $isEnabled, $supportedVersions, $enabledVersions);

        return $response;
    }

    private function isValidVersionFormat(string $version): bool
    {
        return (bool) preg_match(self::VERSION_PATTERN, $version);
    }

    private function parseVersion(string $version): array
    {
        $parts = explode('.', $version);

        return [
            'major' => (int) $parts[0],
            'minor' => (int) $parts[1],
            'patch' => (int) $parts[2],
        ];
    }

    private function unsupportedVersionResponse(array $supportedVersions, array $enabledVersions): Response
    {
        return response()->json([
            'status' => 'error',
            'message' => trans('Unsupported API version. Supported versions: :versions', [
                'versions' => implode(', ', $supportedVersions),
            ]),
            'supported_versions' => $supportedVersions,
            'enabled_versions' => $enabledVersions,
        ], Response::HTTP_BAD_REQUEST);
    }

    private function disabledVersionResponse(string $version, array $supportedVersions, array $enabledVersions): Response
    {
        $response = response()->json([
            'status' => 'success',
            'version_enabled' => false,
            'message' => trans('This API version is currently disabled. Features for this version are not yet available.'),
            'current_version' => $version,
            'supported_versions' => $supportedVersions,
            'enabled_versions' => $enabledVersions,
            'data' => null,
        ], Response::HTTP_OK);

        $this->addVersionHeaders($response, $version, false, $supportedVersions, $enabledVersions);

        return $response;
    }

    private function addVersionHeaders(
        Response $response,
        string $version,
        bool $isEnabled,
        array $supportedVersions,
        array $enabledVersions
    ): void {
        $response->headers->set('X-API-Version', $version);
        $response->headers->set('X-API-Version-Enabled', $isEnabled ? 'true' : 'false');
        $response->headers->set('X-API-Supported-Versions', implode(', ', $supportedVersions));
        $response->headers->set('X-API-Enabled-Versions', implode(', ', $enabledVersions));
    }
}
