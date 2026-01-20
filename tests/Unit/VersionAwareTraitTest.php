<?php

declare(strict_types=1);

use JeromeJHipolito\ApiVersioning\Traits\VersionAwareTrait;

beforeEach(function () {
    request()->attributes->set('api_version', '1.5.0');
    request()->attributes->set('api_major_version', 1);
    request()->attributes->set('api_minor_version', 5);
    request()->attributes->set('api_patch_version', 0);
});

it('gets the current API version', function () {
    $class = new class {
        use VersionAwareTrait;

        public function version(): string
        {
            return $this->getApiVersion();
        }
    };

    expect($class->version())->toBe('1.5.0');
});

it('checks if version is at least specified version', function () {
    $class = new class {
        use VersionAwareTrait;

        public function check(string $v): bool
        {
            return $this->isVersionAtLeast($v);
        }
    };

    expect($class->check('1.0.0'))->toBeTrue();
    expect($class->check('1.5.0'))->toBeTrue();
    expect($class->check('2.0.0'))->toBeFalse();
});

it('checks if version is below specified version', function () {
    $class = new class {
        use VersionAwareTrait;

        public function check(string $v): bool
        {
            return $this->isVersionBelow($v);
        }
    };

    expect($class->check('1.0.0'))->toBeFalse();
    expect($class->check('2.0.0'))->toBeTrue();
});

it('checks if version is between range', function () {
    $class = new class {
        use VersionAwareTrait;

        public function check(string $min, string $max): bool
        {
            return $this->isVersionBetween($min, $max);
        }
    };

    expect($class->check('1.0.0', '2.0.0'))->toBeTrue();
    expect($class->check('1.5.0', '1.5.0'))->toBeTrue();
    expect($class->check('2.0.0', '3.0.0'))->toBeFalse();
});
