<?php

declare(strict_types=1);

namespace JeromeJHipolito\ApiVersioning\Traits;

trait VersionAwareResourceTrait
{
    protected function getApiVersion(): string
    {
        return request()->attributes->get('api_version', config('api-versioning.default_version', '1.0.0'));
    }

    protected function mergeWhenVersion(string $minVersion, array $value): array
    {
        $currentVersion = $this->getApiVersion();

        return version_compare($currentVersion, $minVersion, '>=') ? $value : [];
    }

    protected function mergeWhenVersionBelow(string $maxVersion, array $value): array
    {
        $currentVersion = $this->getApiVersion();

        return version_compare($currentVersion, $maxVersion, '<') ? $value : [];
    }

    protected function mergeWhenVersionExactly(string $version, array $value): array
    {
        return $this->getApiVersion() === $version ? $value : [];
    }

    protected function mergeWhenVersionBetween(string $minVersion, string $maxVersion, array $value): array
    {
        $currentVersion = $this->getApiVersion();
        $isInRange = version_compare($currentVersion, $minVersion, '>=') &&
                     version_compare($currentVersion, $maxVersion, '<=');

        return $isInRange ? $value : [];
    }

    protected function whenVersion(string $minVersion, mixed $value, mixed $default = null): mixed
    {
        $currentVersion = $this->getApiVersion();

        return version_compare($currentVersion, $minVersion, '>=') ? $value : $default;
    }

    protected function whenVersionBelow(string $maxVersion, mixed $value, mixed $default = null): mixed
    {
        $currentVersion = $this->getApiVersion();

        return version_compare($currentVersion, $maxVersion, '<') ? $value : $default;
    }
}
