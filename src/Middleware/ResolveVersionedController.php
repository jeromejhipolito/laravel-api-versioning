<?php

declare(strict_types=1);

namespace JeromeJHipolito\ApiVersioning\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpFoundation\Response;

class ResolveVersionedController
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        $majorVersion = $request->attributes->get('api_major_version', 1);

        $versionedControllers = config('api-versioning.versioned_controllers', []);

        if ($route instanceof Route && isset($versionedControllers[$majorVersion])) {
            $controller = $route->getController();

            if ($controller !== null) {
                $originalClass = get_class($controller);

                if (isset($versionedControllers[$majorVersion][$originalClass])) {
                    $newClass = $versionedControllers[$majorVersion][$originalClass];

                    if (class_exists($newClass)) {
                        $route->controller = app($newClass);
                    }
                }
            }
        }

        return $next($request);
    }
}
