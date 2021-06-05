<?php

use Codememory\Routing\Route;
use Codememory\Routing\Router;
use Codememory\Routing\RouteRedirection;

if (!function_exists('route')) {
    /**
     * @param string $routeName
     *
     * @return Route|null
     */
    function route(string $routeName): ?Route
    {

        return Router::getRouteByName($routeName);

    }
}

if (!function_exists('routePath')) {
    /**
     * @param string $routeName
     * @param array  $parameters
     *
     * @return string|null
     */
    function routePath(string $routeName, array $parameters = []): ?string
    {

        if (null === Router::getRouteByName($routeName)) {
            return null;
        }

        return Router::getRouteByName($routeName)->getResources()->getPathGenerator()->generate($parameters);

    }
}

if (!function_exists('routeRedirection')) {
    /**
     * @param string $routeName
     * @param array  $parameters
     * @param int    $status
     * @param array  $headers
     *
     * @return void
     */
    function routeRedirection(string $routeName, array $parameters = [], int $status = 302, array $headers = []): void
    {

        new RouteRedirection(route($routeName), $parameters, $status, $headers);

    }
}