<?php

namespace Codememory\Routing;

use Codememory\HttpFoundation\Interfaces\RequestInterface;
use Codememory\HttpFoundation\Interfaces\ResponseInterface;
use Codememory\Routing\Exceptions\SoftwareWithoutParentException;
use Codememory\Routing\Exceptions\UndefinedSoftwareException;
use Codememory\Routing\Interfaces\SoftwareDataInterface;
use ReflectionClass;
use ReflectionException;

/**
 * Class Software
 * @package Codememory\Routing
 *
 * @author  Codememory
 */
class Software
{

    public const DELIMITER_CHAR_METHOD_NAME = ':';

    /**
     * @var Utils
     */
    private Utils $utils;

    /**
     * @var array
     */
    private array $software;

    /**
     * @var bool
     */
    private bool $softwareProcessingStatus = true;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var ResponseInterface
     */
    private ResponseInterface $response;

    /**
     * Software constructor.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param Utils             $utils
     * @param array             $software
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, Utils $utils, array $software)
    {

        $this->request = $request;
        $this->response = $response;
        $this->utils = $utils;
        $this->software = $software;

    }

    /**
     * @return $this
     */
    public function make(): Software
    {

        $namespace = trim($this->utils->getBasicSettings()['softwareNamespace'], '\\') . '\\';

        $this->iterationSoftware(function (SoftwareDataInterface $softwareData) use ($namespace) {
            $fullNamespace = $namespace . $softwareData->getSoftwareName();

            if (!$this->checkParentSoftware($fullNamespace)) {
                throw new SoftwareWithoutParentException($fullNamespace);
            }

            $this->invokeSoftwareMethod($fullNamespace, $softwareData->getSoftwareMethod());
        });

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Calls the software method to work if the software returns false in response,
     * sends the result of the process method from the SoftwareLieHandlerInterface
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $fullNamespace
     * @param string $method
     *
     * @throws ReflectionException
     */
    private function invokeSoftwareMethod(string $fullNamespace, string $method): void
    {

        $softwareLieHandler = new SoftwareLieHandler();

        $reflector = $this->getReflector($fullNamespace)->newInstanceArgs([
            $this->request,
            &$softwareLieHandler
        ]);
        $statusExecuteSoftware = call_user_func([$reflector, $method]);

        if (!$statusExecuteSoftware) {
            $this->response->setContent($softwareLieHandler->getProcess())->sendContent();
        }

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the status of the software development
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function getSoftwareProcessingStatus(): bool
    {

        return $this->softwareProcessingStatus;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check whether the software has inherited the abstract software class,
     * if it has inherited it will return true otherwise false
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $softwareNamespace
     *
     * @return bool
     * @throws UndefinedSoftwareException
     * @throws ReflectionException
     */
    private function checkParentSoftware(string $softwareNamespace): bool
    {

        if ($this->existSoftware($softwareNamespace)) {
            $parent = $this->getReflector($softwareNamespace)->getParentClass();

            if (!$parent || $parent->getName() !== SoftwareAbstract::class) {
                return false;
            }

            return true;
        }

        return false;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Return reflector software by namespace
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $softwareNamespace
     *
     * @return ReflectionClass
     * @throws ReflectionException
     */
    private function getReflector(string $softwareNamespace): ReflectionClass
    {

        return new ReflectionClass($softwareNamespace);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check the existence of the software, if it does not exist, an exception
     * will be thrown otherwise it will return true
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $softwareNamespace
     *
     * @return bool
     * @throws UndefinedSoftwareException
     */
    private function existSoftware(string $softwareNamespace): bool
    {

        if (!class_exists($softwareNamespace)) {
            throw new UndefinedSoftwareException($softwareNamespace);
        }

        return true;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Iterate over all software and call the callback handler within each iteration
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param callable $handler
     *
     * @return void
     */
    private function iterationSoftware(callable $handler): void
    {

        foreach ($this->software as $software) {
            call_user_func($handler, new SoftwareData($software));
        }

    }

}