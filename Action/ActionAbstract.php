<?php

namespace Codememory\Routing\Action;

use Codememory\Container\DependencyInjection\Interfaces\DependencyInjectionInterface;
use Codememory\Routing\OutputParameters;
use Codememory\Support\Str;
use Exception;

/**
 * Class ActionAbstract
 * @package Codememory\Routing\Action
 *
 * @author  Codememory
 */
abstract class ActionAbstract
{

    /**
     * @var mixed
     */
    protected mixed $action = null;

    /**
     * @var OutputParameters|null
     */
    protected ?OutputParameters $outputParameters = null;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>
     * Set route action
     * <=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param mixed $action
     *
     * @return ActionAbstract
     */
    public function setAction(mixed $action): ActionAbstract
    {

        $this->action = $action;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Set the OutputParameters object this object is responsible for returning
     * parameter values when opening a route in a browser
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param OutputParameters $outputParameters
     *
     * @return $this
     */
    public function setOutputParameters(OutputParameters $outputParameters): ActionAbstract
    {

        $this->outputParameters = $outputParameters;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Collect and get the route action indicator for the DI container
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|null $name
     *
     * @return string
     * @throws Exception
     */
    public function composeAndGetDependencyId(?string $name = null): string
    {

        return sprintf('%s%s', ActionHandler::DI_ID_PREFIX, $name ?: Str::random(36));

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * In this method, the action of the route itself is processed
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param DependencyInjectionInterface $dependencyInjection
     *
     * @return mixed
     */
    abstract public function call(DependencyInjectionInterface $dependencyInjection): mixed;

}