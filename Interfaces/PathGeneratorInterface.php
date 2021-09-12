<?php

namespace Codememory\Routing\Interfaces;

/**
 * Interface PathGeneratorInterface
 * @package Codememory\Routing\Interfaces
 *
 * @author  Codememory
 */
interface PathGeneratorInterface
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the full path of the route
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the regular expression of the route path, as the first argument,
     * you need to pass an array of rules, parameters
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $expectedParameters
     *
     * @return string
     */
    public function getRegexPath(array $expectedParameters): string;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the path of the route, as an argument you need to pass an array of parameters,
     * where the key - is the name of the parameter
     * value - parameter value
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $expectedParameters
     *
     * @return string
     */
    public function generate(array $expectedParameters = []): string;

}