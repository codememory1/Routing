<?php

namespace Codememory\Routing;

use Codememory\HttpFoundation\Interfaces\RequestInterface;
use Codememory\HttpFoundation\Interfaces\ResponseInterface;
use Codememory\Routing\Action\ActionHandler;
use Codememory\Routing\Interfaces\RouteInterface;
use Codememory\Routing\Interfaces\RouteResourcesInterface;
use Codememory\Routing\Traits\RouteVerificationTrait;

/**
 * Class Route
 * @package Codememory\Routing
 *
 * @author  Codememory
 */
class Route implements RouteInterface
{

    use RouteVerificationTrait;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var ResponseInterface
     */
    private ResponseInterface $response;

    /**
     * @var RouteResourcesInterface
     */
    private RouteResourcesInterface $resources;

    /**
     * @var array
     */
    private array $with = [];

    /**
     * @var string|null
     */
    private ?string $name = null;

    /**
     * @var array
     */
    private array $software = [];

    /**
     * @var array|string[]
     */
    private array $schemes = ['http', 'https'];

    /**
     * @var string|null
     */
    private ?string $method = null;

    /**
     * @var ?bool
     */
    private ?bool $statusVerifyRoute = null;

    /**
     * Route constructor.
     *
     * @param RequestInterface        $request
     * @param ResponseInterface       $response
     * @param RouteResourcesInterface $resources
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, RouteResourcesInterface $resources)
    {

        $this->request = $request;
        $this->response = $response;
        $this->resources = $resources;

    }

    /**
     * @inheritDoc
     */
    public function with(string $parameterName, string $regex = null): RouteInterface
    {

        $this->with[$parameterName] = $regex ?: InputParameters::DEFAULT_PARAMETER_REGEX;

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function name(string $name): RouteInterface
    {

        $this->name = $name;

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function software(array $software): RouteInterface
    {

        $this->software = array_merge($this->software, $software);

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function scheme(array $schemes): RouteInterface
    {

        $this->schemes = $schemes;

        return $this;

    }

    /**
     * @param string $method
     *
     * @return $this
     */
    public function setMethod(string $method): Route
    {

        $this->method = $method;

        return $this;

    }

    /**
     * @return string
     */
    public function getMethod(): string
    {

        return $this->method;

    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {

        return $this->resources->getNamePrefix() . $this->name;

    }

    /**
     * @return array
     */
    public function getRequiredParameters(): array
    {

        return $this->with;

    }

    /**
     * @return array
     */
    public function getSoftware(): array
    {

        return array_merge($this->software, $this->resources->getSoftware());

    }

    /**
     * @return array
     */
    public function getSchemes(): array
    {

        return $this->schemes;

    }

    /**
     * @return OutputParameters
     */
    public function getOutputParameters(): OutputParameters
    {

        return new OutputParameters($this->resources->getPathGenerator(), $this->request->url, $this->getRequiredParameters());

    }

    /**
     * @return RouteResourcesInterface
     */
    public function getResources(): RouteResourcesInterface
    {

        return $this->resources;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The method calls in itself all methods of route verification, if the route
     * has passed all the verifications, the route action handler will be called
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param Utils $utils
     *
     * @return bool
     */
    public function checkValidityRoute(Utils $utils): bool
    {

        $routePathRegex = $this->resources->getPathGenerator()->getRegexPath($this->getRequiredParameters());

        $this
            ->verifyByRoutePathRegex($routePathRegex)
            ->verifyProtocol()
            ->verifyHeaders()
            ->verifySoftware($utils);

        $this->callRouteAction();

        return $this->statusVerifyRoute;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Calls a route action handler that will be executed if the
     * route is the one you were looking for
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return void
     */
    private function callRouteAction(): void
    {

        if ($this->statusVerifyRoute) {
            $action = new ActionHandler($this->getOutputParameters(), $this->resources);

            $action->performAction();
        }

    }

}