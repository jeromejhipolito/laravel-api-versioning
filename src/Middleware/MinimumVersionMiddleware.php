<?php

declare(strict_types=1);

namespace JeromeJHipolito\ApiVersioning\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MinimumVersionMiddleware
{
    public function handle(Request $request, Closure $next, string $minimumVersion): Response
    {
        $currentVersion = $request->header('X-API-Version', config('api-versioning.default_version', '1.0.0'));

        if (version_compare($currentVersion, $minimumVersion, '<')) {
            return response()->json([
                'message'         => trans('This endpoint requires API version :version or higher', ['version' => $minimumVersion]),
                'current_version' => $currentVersion,
                'minimum_version' => $minimumVersion,
            ], Response::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}
