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
     *
     * @return RouterInterface
     */
    public function create(string $path, string $controller): RouterInterface
    {

        $this->routeCreator($path, $controller);

        return $this->router;

    }

    /**
     * @param string $path
     * @param string $controller
     *
     * @return void
     */
    private function routeCreator(string $path, string $controller): void
    {

        $path = rtrim($path, '/');
        $pathWithId = sprintf('%s/:id', $path);

        $this->router->get($path, $this->collectAction($controller, 'all'))->name('all');
        $this->router->get($pathWithId, $this->collectAction($controller, 'show'))
            ->with('id', '[0-9]+')
            ->name('show');
        $this->router->post($path, $this->collectAction($controller, 'create'))->name('create');
        $this->router->put($pathWithId, $this->collectAction($controller, 'update'))
            ->with('id', '[0-9]+')
            ->name('update');
        $this->router->delete($pathWithId, $this->collectAction($controller, 'delete'))
            ->with('id', '[0-9]+')
            ->name('delete');

        $this->router->options($path);
        $this->router->options($pathWithId);

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