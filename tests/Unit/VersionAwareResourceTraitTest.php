<?php

declare(strict_types=1);

use JeromeJHipolito\ApiVersioning\Traits\VersionAwareResourceTrait;

beforeEach(function () {
    request()->attributes->set('api_version', '1.5.0');
});

it('merges when version is at least specified', function () {
    $class = new class {
        use VersionAwareResourceTrait;

        public function test(string $v, array $data): array
        {
            return $this->mergeWhenVersion($v, $data);
        }
    };

    expect($class->test('1.0.0', ['key' => 'value']))->toBe(['key' => 'value']);
    expect($class->test('2.0.0', ['key' => 'value']))->toBe([]);
});

it('merges when version is below specified', function () {
    $class = new class {
        use VersionAwareResourceTrait;

        public function test(string $v, array $data): array
        {
            return $this->mergeWhenVersionBelow($v, $data);
        }
    };

    expect($class->test('2.0.0', ['key' => 'value']))->toBe(['key' => 'value']);
    expect($class->test('1.0.0', ['key' => 'value']))->toBe([]);
});

it('returns value when version matches', function () {
    $class = new class {
        use VersionAwareResourceTrait;

        public function test(string $v, mixed $value, mixed $default): mixed
        {
            return $this->whenVersion($v, $value, $default);
        }
    };

    expect($class->test('1.0.0', 'new', 'old'))->toBe('new');
    expect($class->test('2.0.0', 'new', 'old'))->toBe('old');
});
