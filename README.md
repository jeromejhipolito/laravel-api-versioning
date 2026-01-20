# Laravel API Versioning

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeromejhipolito/laravel-api-versioning.svg?style=flat-square)](https://packagist.org/packages/jeromejhipolito/laravel-api-versioning)
[![License](https://img.shields.io/packagist/l/jeromejhipolito/laravel-api-versioning.svg?style=flat-square)](https://packagist.org/packages/jeromejhipolito/laravel-api-versioning)

Header-based API versioning with version flags for Laravel. Supports semantic versioning and feature flag-like version control.

## Features

- 🏷️ **Header-based versioning** - Uses `X-API-Version` header (industry standard like Stripe, GitHub)
- 📦 **Semantic versioning** - Full support for major.minor.patch format (1.0.0, 1.1.0, 2.0.0)
- 🚩 **Version flags** - Enable/disable versions independently (perfect for app store review periods)
- 🔄 **Controller inheritance** - Override only changed methods in versioned controllers
- 📱 **Mobile-friendly** - Disabled versions return `version_enabled: false` so apps can hide features
- 🌍 **Translations** - Built-in support for English, Japanese, and Korean

## Installation

```bash
composer require jeromejhipolito/laravel-api-versioning
```

## Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=api-versioning-config
php artisan vendor:publish --tag=api-versioning-lang
```

## Configuration

Add to your `.env`:

```env
# Optional: Comma-separated list of enabled versions (null = all enabled)
API_ENABLED_VERSIONS=1.0.0,1.1.0

# Optional: Default version when header is missing
API_DEFAULT_VERSION=1.0.0
```

Edit `config/api-versioning.php`:

```php
return [
    'supported_versions' => ['1.0.0', '1.1.0', '2.0.0'],
    'enabled_versions' => env('API_ENABLED_VERSIONS') 
        ? array_map('trim', explode(',', env('API_ENABLED_VERSIONS')))
        : null, // null = all supported versions enabled
    'default_version' => env('API_DEFAULT_VERSION', '1.0.0'),
];
```

## Register Middleware

In `bootstrap/app.php`:

```php
use JeromeJHipolito\ApiVersioning\Middleware\ApiVersionMiddleware;
use JeromeJHipolito\ApiVersioning\Middleware\ResolveVersionedController;

->withMiddleware(function (Middleware $middleware) {
    $middleware->api(append: [
        ApiVersionMiddleware::class,
        ResolveVersionedController::class,
    ]);
})
```

## Usage

### Making Requests

```bash
# With version header
curl -H "X-API-Version: 1.0.0" https://api.example.com/users

# Without header (uses default version)
curl https://api.example.com/users
```

### Response Headers

Every response includes:
- `X-API-Version: 1.0.0`
- `X-API-Version-Enabled: true`
- `X-API-Supported-Versions: 1.0.0, 1.1.0, 2.0.0`
- `X-API-Enabled-Versions: 1.0.0, 1.1.0`

### Check Version Status

```bash
GET /api/version/status
```

```json
{
  "status": "success",
  "data": {
    "current_version": "1.0.0",
    "version_enabled": true,
    "default_version": "1.0.0",
    "supported_versions": ["1.0.0", "1.1.0", "2.0.0"],
    "enabled_versions": ["1.0.0", "1.1.0"],
    "version_flags": {
      "1.0.0": true,
      "1.1.0": true,
      "2.0.0": false
    }
  }
}
```

### Using in Controllers

```php
use JeromeJHipolito\ApiVersioning\Traits\VersionAwareTrait;

class UserController extends Controller
{
    use VersionAwareTrait;

    public function show($id)
    {
        $user = User::find($id);
        
        // Check version
        if ($this->isVersionAtLeast('2.0.0')) {
            return new V2\UserResource($user);
        }
        
        return new UserResource($user);
    }
}
```

### Using in Resources

```php
use JeromeJHipolito\ApiVersioning\Traits\VersionAwareResourceTrait;

class UserResource extends JsonResource
{
    use VersionAwareResourceTrait;

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            
            // Only in 1.1.0+
            ...$this->mergeWhenVersion('1.1.0', [
                'profile_score' => $this->profile_score,
            ]),
            
            // Only below 2.0.0 (deprecated)
            ...$this->mergeWhenVersionBelow('2.0.0', [
                'legacy_field' => $this->old_data,
            ]),
        ];
    }
}
```

### Disabled Version Response

When a version is supported but not enabled:

```json
{
  "status": "success",
  "version_enabled": false,
  "message": "This API version is currently disabled...",
  "current_version": "2.0.0",
  "data": null
}
```

This allows mobile apps to check `version_enabled` and hide features accordingly.

## Version Flags Workflow

1. **Add new version as supported but disabled**
   ```php
   'supported_versions' => ['1.0.0', '2.0.0'],
   ```
   ```env
   API_ENABLED_VERSIONS=1.0.0
   ```

2. **Deploy to production** - Old apps continue working

3. **Submit new app version** - App checks `version_flags` and hides 2.0.0 features

4. **After app store approval** - Enable the version
   ```env
   API_ENABLED_VERSIONS=1.0.0,2.0.0
   ```

## Available Methods

### VersionAwareTrait (Controllers)

| Method | Description |
|--------|-------------|
| `getApiVersion()` | Get current version string |
| `getApiMajorVersion()` | Get major version number |
| `getApiMinorVersion()` | Get minor version number |
| `getApiPatchVersion()` | Get patch version number |
| `isVersionAtLeast($v)` | Check if >= version |
| `isVersionBelow($v)` | Check if < version |
| `isVersionExactly($v)` | Check if exact version |
| `isVersionBetween($min, $max)` | Check if in range |

### VersionAwareResourceTrait (Resources)

| Method | Description |
|--------|-------------|
| `mergeWhenVersion($v, $array)` | Merge array if >= version |
| `mergeWhenVersionBelow($v, $array)` | Merge array if < version |
| `mergeWhenVersionExactly($v, $array)` | Merge array if exact version |
| `mergeWhenVersionBetween($min, $max, $array)` | Merge if in range |
| `whenVersion($v, $value, $default)` | Return value if >= version |
| `whenVersionBelow($v, $value, $default)` | Return value if < version |

## License

MIT License. See [LICENSE](LICENSE) for details.
