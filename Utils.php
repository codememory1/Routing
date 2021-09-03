<?php

namespace Codememory\Routing;

use Codememory\Components\Configuration\Configuration;
use Codememory\Components\Configuration\Interfaces\ConfigInterface;
use Codememory\Components\GlobalConfig\GlobalConfig;
use Codememory\Routing\Exceptions\IncorrectControllerException;
use Codememory\Routing\Exceptions\InvalidControllerMethodException;
use Codememory\Support\Arr;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class Utils
 *
 * @package Codememory\Routing
 *
 * @author  Codememory
 */
class Utils
{

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * Utils constructor.
     */
    public function __construct()
    {

        $this->config = Configuration::getInstance()->open(GlobalConfig::get('routing.configName'), $this->getDefaultConfig());

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an array of basic routing settings
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function getBasicSettings(): array
    {

        return $this->config->get('_settings');

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an array of all created routes in configuration
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     * @throws IncorrectControllerException
     * @throws InvalidControllerMethodException
     */
    public function getRoutes(): array
    {

        $routes = [];

        foreach ($this->config->get('_routes') as $routeName => $routeData) {
            $path = $routeData['path'] ?? '/';
            $requestMethod = $routeData['method'] ?? 'GET';
            $controller = $this->existAndGetController(Arr::set($routeData)::get('class.controller'));
            $method = $this->existAndGetControllerMethod($controller, Arr::set($routeData)::get('class.method'));
            $parameters = $routeData['parameters'] ?? [];
            $software = $routeData['software'] ?? [];
            $schemes = $routeData['schemes'] ?? [];

            $routes[$routeName] = $this->getRouteStructure($path, $requestMethod, $controller, $method, $parameters, $software, $schemes);
        }

        return $routes;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns information about the created route in the configuration
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $routeName
     *
     * @return array
     * @throws IncorrectControllerException
     * @throws InvalidControllerMethodException
     */
    public function getRouteData(string $routeName): array
    {

        return $this->getRoutes()[$routeName] ?? [];

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the data structure for the route
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $path
     * @param string $requestMethod
     * @param string $controller
     * @param string $method
     * @param array  $parameters
     * @param array  $software
     * @param array  $schemes
     *
     * @return array
     */
    #[ArrayShape(['path' => "string", 'method' => "string", 'class' => "string[]", 'parameters' => "array", 'software' => "array", 'schemes' => "array"])]
    public function getRouteStructure(string $path, string $requestMethod, string $controller, string $method, array $parameters, array $software, array $schemes): array
    {

        return [
            'path'       => $path,
            'method'     => $requestMethod,
            'class'      => [
                'controller' => $controller,
                'method'     => $method,
            ],
            'parameters' => $parameters,
            'software'   => $software,
            'schemes'    => $schemes
        ];

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Checks for the existence of the controller, if it does not exist, an
     * exception will be thrown otherwise the namespace of the controller
     * will be returned
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|null $controller
     *
     * @return string
     * @throws IncorrectControllerException
     */
    public function existAndGetController(?string $controller): string
    {

        if (empty($controller) || !class_exists($controller)) {
            throw new IncorrectControllerException($controller);
        }

        return $controller;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Checks for the existence of a method in the controller, if the
     * method exists, returns its name otherwise an exception
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|null $controller
     * @param string|null $method
     *
     * @return string
     * @throws IncorrectControllerException
     * @throws InvalidControllerMethodException
     */
    public function existAndGetControllerMethod(?string $controller, ?string $method): string
    {

        if ($this->existAndGetController($controller) && !empty($method) && method_exists($controller, $method)) {
            return $method;
        }

        throw new InvalidControllerMethodException($controller, $method);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns a data structure, basic settings that will be returned if
     * the configuration does not exist
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    #[ArrayShape(['_settings' => "mixed", '_routes' => "array"])]
    private function getDefaultConfig(): array
    {

        return [
            '_settings' => GlobalConfig::get('routing.settings'),
            '_routes'   => []
        ];

    }

}