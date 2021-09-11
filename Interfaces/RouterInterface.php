<?php

namespace Codememory\Routing\Interfaces;

use Codememory\Routing\Route;

/**
 * Interface RouterInterface
 * @package Codememory\Routing\Interfaces
 *
 * @author  Codememory
 */
interface RouterInterface
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Create a route for the GET method. The first argument is url,
     * the second argument is a handler, callback or namespace and
     * the method is example: App.Controllers.Main # method
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string          $path
     * @param callable|string $action
     *
     * @return RouteInterface
     */
    public static function get(string $path, callable|string $action): RouteInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Create a route for the POST request method
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string          $path
     * @param callable|string $action
     *
     * @return RouteInterface
     */
    public static function post(string $path, callable|string $action): RouteInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Create a route with any request method GET|POST|FETCH|PUT
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string          $path
     * @param callable|string $action
     *
     * @return RouteInterface
     */
    public static function any(string $path, callable|string $action): RouteInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Create a route for the FETCH request method
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string          $path
     * @param callable|string $action
     *
     * @return RouteInterface
     */
    public static function fetch(string $path, callable|string $action): RouteInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Create a route for the PUT request method
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string          $path
     * @param callable|string $action
     *
     * @return RouteInterface
     */
    public static function put(string $path, callable|string $action): RouteInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Create a route for the HEAD request method
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string          $path
     * @param callable|string $action
     *
     * @return RouteInterface
     */
    public static function head(string $path, callable|string $action): RouteInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Create a route for the DELETE request method
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string          $path
     * @param callable|string $action
     *
     * @return RouteInterface
     */
    public static function delete(string $path, callable|string $action): RouteInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Create a route for the PATH request method
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string          $path
     * @param callable|string $action
     *
     * @return RouteInterface
     */
    public static function path(string $path, callable|string $action): RouteInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Create a route for the OPTIONS request method
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string          $path
     * @param callable|string $action
     *
     * @return RouteInterface
     */
    public static function options(string $path, callable|string $action): RouteInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * This method includes 4 request methods. GET, POST, PUT, DELETE and has reserved
     * methods in the controller, they can be changed with 3 arguments
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string         $path
     * @param string         $controller
     * @param array|string[] $methods
     *
     * @return RouterInterface
     */
    public static function resource(string $path, string $controller, array $methods = [
        'GET'    => 'show',
        'POST'   => 'create',
        'PUT'    => 'update',
        'DELETE' => 'delete'
    ]): RouterInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Create a route group, a prefix is passed as the first
     * argument, a callback is passed as the second with
     * the creation of routes
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string   $pathPrefix
     * @param callable $callback
     *
     * @return RouterInterface
     */
    public static function group(string $pathPrefix, callable $callback): RouterInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Creating a group of route names? prefix is passed as
     * the first argument, and the callback with routes as
     * the second argument
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string   $namePrefix
     * @param callable $callback
     *
     * @return RouterInterface
     */
    public static function nameGroup(string $namePrefix, callable $callback): RouterInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Create a software group for routes, an array with the
     * software namespaces is passed as the first argument,
     * and a callback with the creation of routes as the second
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array    $software
     * @param callable $callback
     *
     * @return RouterInterface
     */
    public static function softwareGroup(array $software, callable $callback): RouterInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Add routes that will be available to a specific subdomain
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string   $subdomain
     * @param callable $callback
     *
     * @return RouterInterface
     */
    public static function subdomainGroup(string $subdomain, callable $callback): RouterInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check for the existence of a route by name, returns
     * a boolean value
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $routeName
     *
     * @return bool
     */
    public static function routeExist(string $routeName): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Investigates an array of all created routes
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public static function allRoutes(): array;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an object of a specific route by name, if the route
     * does not exist, it will return false
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $name
     *
     * @return Route|bool
     */
    public static function getRouteByName(string $name): Route|bool;

}