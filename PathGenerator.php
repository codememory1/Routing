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
    public function getRegexPath(array $requiredParameters): string
    {

        $routePathParameters = new InputParameters($this->getPath());

        $this->checkRouteParametersInRequired($routePathParameters, $requiredParameters);

        return $this->generatingPathRegexFromRoutePath($requiredParameters);

    }

    /**
     * @inheritDoc
     */
    public function generate(array $parameters = []): string
    {

        $path = $this->routePath;

        foreach ($parameters as $name => $value) {
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
     * @param array $requiredParameters
     *
     * @return string
     */
    private function generatingPathRegexFromRoutePath(array $requiredParameters): string
    {

        $routePathQuote = preg_quote($this->getPath(), '/');

        foreach ($requiredParameters as $name => $regex) {
            $search = sprintf('\:%s', $name);
            $replace = sprintf('(?<%s>%s)', $name, $regex);

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
     * @param array           $requiredParameters
     */
    private function checkRouteParametersInRequired(InputParameters $routeParameters, array &$requiredParameters): void
    {

        foreach ($routeParameters->all() as $parameterName) {
            if (!array_key_exists($parameterName, $requiredParameters)) {
                $requiredParameters[$parameterName] = InputParameters::DEFAULT_PARAMETER_REGEX;
            }
        }

    }

}