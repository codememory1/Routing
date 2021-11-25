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

        $this->router->get($path, [$controller, 'all'], true)->name('all');
        $this->router->get($pathWithId, [$controller, 'show'], true)
            ->with('id', '[0-9]+')
            ->name('show');
        $this->router->post($path, [$controller, 'create'], true)->name('create');
        $this->router->put($pathWithId, [$controller, 'update'], true)
            ->with('id', '[0-9]+')
            ->name('update');
        $this->router->delete($pathWithId, [$controller, 'delete'], true)
            ->with('id', '[0-9]+')
            ->name('delete');

    }

}