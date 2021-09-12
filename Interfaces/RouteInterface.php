<?php

namespace Codememory\Routing\Interfaces;

/**
 * Interface RouteInterface
 * @package Codememory\Routing\Interfaces
 *
 * @author  Codememory
 */
interface RouteInterface
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Create rules for a specific route parameter
     * Default regex for parameter: .*
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string      $parameterName
     * @param string|null $regex
     * @param bool        $required
     *
     * @return RouteInterface
     */
    public function with(string $parameterName, string $regex = null, bool $required = true): RouteInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Set a name for the route with which you can get this route
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $name
     *
     * @return RouteInterface
     */
    public function name(string $name): RouteInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Specify an array of routing software,
     * Example: ["<ClassSoftware>:<Method>"]
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $software
     *
     * @return RouteInterface
     */
    public function software(array $software): RouteInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Specify the protocols by which the route will be available
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $schemes
     *
     * @return RouteInterface
     */
    public function scheme(array $schemes): RouteInterface;

}