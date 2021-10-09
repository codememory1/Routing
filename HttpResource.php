<?php

namespace Codememory\Routing;

use Codememory\Routing\Interfaces\RouterInterface;

/**
 * Class HttpResource
 *
 * @package Codememory\Routing
 *
 * @author  Codememory
 */
class HttpResource
{

    /**
     * @var RouterInterface
     */
    private RouterInterface $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {

        $this->router = $router;

    }

    /**
     * @param string $path
     * @param string $controller
     * @param array  $methods
     *
     * @return RouterInterface
     */
    public function create(string $path, string $controller, array $methods): RouterInterface
    {

        $this->routeCreator($path, $controller, $methods);

        return $this->router;

    }

    /**
     * @param string $path
     * @param string $controller
     * @param array  $methods
     */
    private function routeCreator(string $path, string $controller, array $methods): void
    {

        $path = rtrim($path, '/');
        $pathWithId = sprintf('%s/:id', $path);

        $this->router->get($pathWithId, $this->collectAction($controller, $methods['GET']))
            ->name('get')
            ->with('id', '[0-9]+', false);
        $this->router->post($path, $this->collectAction($controller, $methods['POST']))
            ->name('post');
        $this->router->put($pathWithId, $this->collectAction($controller, $methods['PUT']))
            ->with('id', '[0-9]+')
            ->name('put');
        $this->router->delete($pathWithId, $this->collectAction($controller, $methods['DELETE']))
            ->with('id', '[0-9]+')
            ->name('delete');

        $this->router->options($path, function () {
            // No action for this request method
        });
        $this->router->options($pathWithId, function () {
            // No action for this request method
        });

    }

    /**
     * @param string $controller
     * @param string $method
     *
     * @return string
     */
    private function collectAction(string $controller, string $method): string
    {

        return sprintf('%s#%s', $controller, $method);

    }

}