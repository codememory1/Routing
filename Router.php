<?php

namespace Codememory\Routing;

use Codememory\Routing\Action\ActionHandler;
use Codememory\Routing\Interfaces\RouteInterface;
use Codememory\Routing\Interfaces\RouterInterface;
use Codememory\Routing\Traits\ConstructStaticTrait;
use Codememory\Support\Str;
use Generator;
use JetBrains\PhpStorm\Pure;
use RuntimeException;

/**
 * Class Router
 *
 * @package Codememory\Routing
 *
 * @author  Codememory
 */
class Router implements RouterInterface
{

    private const INPUT_NAME_WITH_METHOD = '_method';

    use ConstructStaticTrait;

    /**
     * @var bool
     */
    private static bool $initRoutesFromConfig = false;

    /**
     * @var array
     */
    private static array $routes = [];

    /**
     * @var string|null
     */
    private static ?string $routePathPrefix = null;

    /**
     * @var string|null
     */
    private static ?string $routeNamePrefix = null;

    /**
     * @var array
     */
    private static array $software = [];

    /**
     * @var string|null
     */
    private static ?string $subdomain = null;

    /**
     * @var bool
     */
    private static bool $statusRouteFound = false;

    /**
     * @var Route|null
     */
    private static ?Route $openedRoute = null;

    /**
     * @return Router
     * @throws Exceptions\ConstructorNotInitializedException
     * @throws Exceptions\IncorrectControllerException
     * @throws Exceptions\InvalidControllerMethodException
     */
    public static function initializingRoutesFromConfig(): Router
    {

        self::checkConstructorInitialization();

        if (self::$initRoutesFromConfig) {
            throw new RuntimeException('Cannot call "initializingRoutesFromConfig" method more than once');
        }

        self::$initRoutesFromConfig = true;

        $routes = self::$utils->getRoutes();

        foreach ($routes as $routeName => $routeData) {
            $method = Str::toLowercase($routeData['method']);
            $action = $routeData['class']['controller'] . '#' . $routeData['class']['method'];
            $software = [];

            $route = self::$method($routeData['path'], $action)->name($routeName);

            foreach ($routeData['parameters'] as $parameterName => $regex) {
                $route->with($parameterName, $regex);
            }

            foreach ($routeData['software'] as $softwareClassName => $softwareMethod) {
                $software[] = $softwareClassName . Software::DELIMITER_CHAR_METHOD_NAME . $softwareMethod;
            }

            if ([] !== $software) {
                $route->software($software);
            }

            if ([] !== $routeData['schemes']) {
                $route->scheme($routeData['schemes']);
            }

        }

        return new self();

    }

    /**
     * @inheritDoc
     */
    public static function get(string $path, callable|string $action): RouteInterface
    {

        return self::routeCollector($path, ['GET'], $action);

    }

    /**
     * @inheritDoc
     */
    public static function post(string $path, callable|string $action): RouteInterface
    {

        return self::routeCollector($path, ['POST'], $action);

    }

    /**
     * @inheritDoc
     */
    public static function any(string $path, callable|string $action): RouteInterface
    {

        return self::routeCollector($path, [
            'GET', 'POST', 'FETCH',
            'PUT', 'HEAD', 'PATH',
            'OPTIONS', 'DELETE'
        ], $action);

    }

    /**
     * @inheritDoc
     */
    public static function fetch(string $path, callable|string $action): RouteInterface
    {

        return self::routeCollector($path, ['FETCH'], $action, ['X-Requested-With' => 'XMLHttpRequest']);

    }

    /**
     * @inheritDoc
     */
    public static function put(string $path, callable|string $action): RouteInterface
    {

        return self::routeCollector($path, ['PUT'], $action);

    }

    /**
     * @inheritDoc
     */
    public static function head(string $path, callable|string $action): RouteInterface
    {

        return self::routeCollector($path, ['HEAD'], $action);

    }

    /**
     * @inheritDoc
     */
    public static function delete(string $path, callable|string $action): RouteInterface
    {

        return self::routeCollector($path, ['DELETE'], $action);

    }

    /**
     * @inheritDoc
     */
    public static function path(string $path, callable|string $action): RouteInterface
    {

        return self::routeCollector($path, ['PATH'], $action);

    }

    /**
     * @inheritDoc
     */
    public static function options(string $path, callable|string $action): RouteInterface
    {

        return self::routeCollector($path, ['OPTIONS'], $action);

    }

    /**
     * @inheritDoc
     */
    public static function resource(string $path, string $controller, array $methods = [
        'GET'    => 'show',
        'POST'   => 'create',
        'PUT'    => 'update',
        'DELETE' => 'delete'
    ]): RouterInterface
    {

        $path = rtrim($path, '/');
        $pathWithId = sprintf('%s/:id', $path);

        self::get($pathWithId, sprintf('%s#%s', $controller, $methods['GET']))
            ->with('id', '(\/[0-9]+)?')
            ->name('get');
        self::post($path, sprintf('%s#%s', $controller, $methods['POST']))
            ->name('post');
        self::put($pathWithId, sprintf('%s#%s', $controller, $methods['PUT']))
            ->with('id', '[0-9]+')
            ->name('put');
        self::delete($pathWithId, sprintf('%s#%s', $controller, $methods['DELETE']))
            ->with('id', '[0-9]+')
            ->name('delete');

        return new self();

    }

    /**
     * @inheritDoc
     */
    public static function group(string $pathPrefix, callable $callback): RouterInterface
    {

        return self::prefixHandler($pathPrefix, $callback, 'routePathPrefix');

    }

    /**
     * @inheritDoc
     */
    public static function nameGroup(string $namePrefix, callable $callback): RouterInterface
    {

        return self::prefixHandler($namePrefix, $callback, 'routeNamePrefix');

    }

    /**
     * @inheritDoc
     */
    public static function softwareGroup(array $software, callable $callback): RouterInterface
    {

        $previousSoftware = self::$software;

        self::$software = array_merge(self::$software, $software);

        call_user_func($callback);

        self::$software = $previousSoftware;

        return new self();

    }

    /**
     * @inheritDoc
     */
    public static function subdomainGroup(string $subdomain, callable $callback): RouterInterface
    {

        self::$subdomain = $subdomain;

        call_user_func($callback);

        self::$subdomain = null;

        return new self();

    }

    /**
     * @inheritDoc
     */
    public static function routeExist(string $routeName): bool
    {

        foreach (self::allRoutes() as $route) {
            if ($route->getName() === $routeName) {
                return true;
            }
        }

        return false;

    }

    /**
     * @inheritDoc
     */
    public static function allRoutes(): array
    {

        $routesWithoutHttpMethod = [];

        foreach (self::$routes as $routes) {
            foreach ($routes as $route) {
                $routesWithoutHttpMethod[] = $route;
            }
        }

        return $routesWithoutHttpMethod;

    }

    /**
     * @inheritDoc
     */
    public static function getRouteByName(string $name): Route|bool
    {

        if (self::routeExist($name)) {
            foreach (self::allRoutes() as $route) {
                if ($route->getName() === $name) {
                    return $route;
                }
            }
        }

        return false;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the currently open route
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return Route|null
     */
    public static function getCurrentRoute(): ?Route
    {

        return self::$openedRoute;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * A method that iterates over all created routes, verifies each route
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return void
     * @throws Exceptions\ConstructorNotInitializedException
     */
    public static function processAllRoutes(): void
    {

        self::checkConstructorInitialization();

        /** @var Route $route */
        foreach (self::iterationRoutes() as $route) {
            self::$statusRouteFound = $route->checkValidityRoute(self::$utils);

            if (self::$statusRouteFound) {
                self::$openedRoute = $route;

                break;
            }
        }

        self::routerResponse();

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the correct assembled route path given the group prefix
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $routePath
     *
     * @return string
     */
    #[Pure]
    public static function collectAndGetRoutePath(string $routePath): string
    {

        $prefix = trim(self::$routePathPrefix, '/') . '/';

        return self::$request->url->getUrl($prefix . $routePath);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Iterate over arrays of all created routes and call callback - handler
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return Generator
     */
    private static function iterationRoutes(): Generator
    {

        foreach (self::$routes[self::getRequestMethod()] ?? [] as $route) {
            yield $route;

            if (self::$statusRouteFound) {
                break;
            }
        }

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The route picker that runs when the route is created. In this method,
     * the created object of the created route is added to the array
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string          $path
     * @param array           $methods
     * @param callable|string $action
     * @param array           $headers
     *
     * @return RouteInterface
     */
    private static function routeCollector(string $path, array $methods, callable|string $action, array $headers = []): RouteInterface
    {

        $methods = array_map(fn (string $method) => Str::toUppercase($method), $methods);

        $pathGenerator = new PathGenerator(self::collectAndGetRoutePath($path));
        $resources = new RouteResources($pathGenerator, $action, $headers, self::$routeNamePrefix, self::$software);

        if (null !== self::$subdomain) {
            $resources->setSubdomain(self::$subdomain);
        }

        $route = new Route(self::$request, self::$response, $resources);

        foreach ($methods as $method) {
            $route->setMethod($method);

            self::$routes[$method][] = $route;
        }

        return $route;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Handler for adding and removing a prefix to any property
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string   $prefix
     * @param callable $callback
     * @param string   $propertyForPrefix
     *
     * @return RouterInterface
     */
    private static function prefixHandler(string $prefix, callable $callback, string $propertyForPrefix): RouterInterface
    {

        $valuePropertyForPrefix = self::$$propertyForPrefix;

        self::$$propertyForPrefix .= $prefix;

        call_user_func($callback);

        self::$$propertyForPrefix = $valuePropertyForPrefix;

        return new self();

    }

    /**
     * @return string
     */
    private static function getRequestMethod(): string
    {

        $requestMethod = self::$request->getMethod();
        $methodFromFormField = self::$request->post()->get(self::INPUT_NAME_WITH_METHOD);

        if (null !== $methodFromFormField) {
            $methodFromFormField = Str::toUppercase($methodFromFormField);
        }

        if ('POST' === $requestMethod && null !== $methodFromFormField) {
            return $methodFromFormField;
        }

        return $requestMethod;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the current request method, if the request method POST method will check the
     * name of the method that is specified in the form input with the name "_method"
     * if this input exists, this particular request method will be returned
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return Router
     */
    private static function scanningAndImportFilesWithRoutes(): Router
    {

        $pathWithRoutes = trim(self::$utils->getBasicSettings()['pathWithRoutes'], '/');
        $routesFileSuffix = self::$utils->getBasicSettings()['routesFileSuffix'];

        if (self::$filesystem->exist($pathWithRoutes)) {
            $filesOfPathWithRoutes = self::$filesystem->scanning($pathWithRoutes);

            foreach ($filesOfPathWithRoutes as $fileOfPathWithRoutes) {
                if (Str::ends($fileOfPathWithRoutes, sprintf('%s.php', $routesFileSuffix))) {
                    self::$filesystem->singleImport($pathWithRoutes . '/' . $fileOfPathWithRoutes);
                }
            }
        }

        return new self();

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The router's response is thrown, if the route is found, then the
     * action will be processed otherwise 404
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return void
     */
    private static function routerResponse(): void
    {

        if (!self::$statusRouteFound) {
            self::$response->setResponseCode(404)->sendHeaders();
        } else {
            self::callRouteAction(self::$statusRouteFound, self::getCurrentRoute());
        }

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Calls a route action handler that will be executed if the
     * route is the one you were looking for
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param bool  $statusVerifyRoute
     * @param Route $route
     *
     * @return void
     */
    private static function callRouteAction(bool $statusVerifyRoute, Route $route): void
    {

        if ($statusVerifyRoute) {
            $action = new ActionHandler($route->getOutputParameters(), $route->getResources());

            $action->performAction();
        }

    }

}
