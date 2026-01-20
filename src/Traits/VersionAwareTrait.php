<?php

declare(strict_types=1);

namespace JeromeJHipolito\ApiVersioning\Traits;

trait VersionAwareTrait
{
    protected function getApiVersion(): string
    {
        return request()->attributes->get('api_version', config('api-versioning.default_version', '1.0.0'));
    }

    protected function getApiMajorVersion(): int
    {
        return request()->attributes->get('api_major_version', 1);
    }

    protected function getApiMinorVersion(): int
    {
        return request()->attributes->get('api_minor_version', 0);
    }

    protected function getApiPatchVersion(): int
    {
        return request()->attributes->get('api_patch_version', 0);
    }

    protected function isApiVersionEnabled(): bool
    {
        return request()->attributes->get('api_version_enabled', true);
    }

    protected function isVersionAtLeast(string $version): bool
    {
        return version_compare($this->getApiVersion(), $version, '>=');
    }

    protected function isVersionBelow(string $version): bool
    {
        return version_compare($this->getApiVersion(), $version, '<');
    }

    protected function isVersionExactly(string $version): bool
    {
        return $this->getApiVersion() === $version;
    }

    protected function isVersionBetween(string $minVersion, string $maxVersion): bool
    {
        $current = $this->getApiVersion();

        return version_compare($current, $minVersion, '>=') && version_compare($current, $maxVersion, '<=');
    }
}
