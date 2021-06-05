<?php

namespace Codememory\Routing;

use Codememory\HttpFoundation\Request\Request;
use Codememory\HttpFoundation\Response\RedirectResponse;

/**
 * Class RouteRedirection
 * @package Codememory\Routing
 *
 * @author  Codememory
 */
class RouteRedirection
{

    /**
     * @var Route
     */
    private Route $route;

    /**
     * RouteRedirection constructor.
     *
     * @param Route $route
     * @param array $parameters
     * @param int   $status
     * @param array $headers
     */
    public function __construct(Route $route, array $parameters = [], int $status = 302, array $headers = [])
    {

        $this->route = $route;

        $this->redirect($parameters, $status, $headers);

    }

    /**
     * @param array $parameters
     * @param int   $status
     * @param array $headers
     */
    private function redirect(array $parameters = [], int $status = 302, array $headers = []): void
    {

        $routePath = $this->route->getResources()->getPathGenerator()->generate($parameters);
        $redirectResponse = new RedirectResponse(new Request());

        $redirectResponse->redirect($routePath, $status, $headers);

    }

}