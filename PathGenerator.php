<?php

namespace Codememory\Routing;

use Codememory\Routing\Interfaces\PathGeneratorInterface;
use Codememory\Support\Str;

/**
 * Class PathGenerator
 * @package Codememory\Routing
 *
 * @author  Codememory
 */
class PathGenerator implements PathGeneratorInterface
{

    /**
     * @var string
     */
    private string $routePath;

    /**
     * PathGenerator constructor.
     *
     * @param string $routePath
     */
    public function __construct(string $routePath)
    {

        $this->routePath = $routePath;

    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {

        return $this->routePath;

    }

    /**
     * @inheritDoc
     */
    public function getRegexPath(array $expectedParameters): string
    {

        $routePathParameters = new InputParameters($this->getPath());

        $this->checkRouteParametersInRequired($routePathParameters, $expectedParameters);

        return $this->generatingPathRegexFromRoutePath($expectedParameters);

    }

    /**
     * @inheritDoc
     */
    public function generate(array $expectedParameters = []): string
    {

        $path = $this->routePath;

        foreach ($expectedParameters as $name => $value) {
            Str::replace($path, sprintf('%s%s', InputParameters::PARAMETER_START_CHARACTER, $name), $value);
        }

        return $path;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Generates the route path - escapes characters in the route that may affect
     * the regex, strip the parameter names from the path and substitute the
     * parameter's regular expression instead of them and return the full
     * path based on the regex
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $expectedParameters
     *
     * @return string
     */
    private function generatingPathRegexFromRoutePath(array $expectedParameters): string
    {

        $routePathQuote = preg_quote($this->getPath(), '/');

        foreach ($expectedParameters as $name => $data) {
            if(array_key_exists('required', $data) && $data['required']) {
                $search = sprintf('\:%s', $name);
                $replace = sprintf('(?<%s>%s)', $name, $data['regex']);
            } else {
                $search = sprintf('\/\:%s', $name);
                $replace = sprintf('(\/(?<%s>%s))?', $name, $data['regex']);
            }

            Str::replace($routePathQuote, $search, $replace);
        }

        return sprintf('/^%s$/', $routePathQuote);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Processes all parameters of the route, if no rule has been created for
     * this parameter, the default rule ". *" Will be assigned to this parameter.
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param InputParameters $routeParameters
     * @param array           $expectedParameters
     */
    private function checkRouteParametersInRequired(InputParameters $routeParameters, array &$expectedParameters): void
    {

        foreach ($routeParameters->all() as $parameterName) {
            if (!array_key_exists($parameterName, $expectedParameters)) {
                $expectedParameters[$parameterName]['regex'] = InputParameters::DEFAULT_PARAMETER_REGEX;
            }
        }

    }

}