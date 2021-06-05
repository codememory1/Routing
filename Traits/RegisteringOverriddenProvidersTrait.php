<?php

namespace Codememory\Routing\Traits;

use Codememory\Components\Configuration\Config;
use Codememory\Container\ServiceProvider\Interfaces\InjectionProviderInterface;
use Codememory\FileSystem\File;
use Codememory\HttpFoundation\Client\Header\Header;
use Codememory\HttpFoundation\Request\Request;
use Codememory\HttpFoundation\Response\Response;

/**
 * Trait RegisteringOverriddenProvidersTrait
 * @package Codememory\Routing\Traits
 *
 * @author  Codememory
 */
trait RegisteringOverriddenProvidersTrait
{

    /**
     * @return void
     */
    private function registerProviders(): void
    {

        $this->serviceProvider
            ->register('config', Config::class, function (InjectionProviderInterface $injectionProvider) {
                $injectionProvider->construct([new File()], true);
            })
            ->register('request', Request::class)
            ->register('response', Response::class, function (InjectionProviderInterface $injectionProvider) {
                $injectionProvider->construct([new Header()], true);
            });

        $this->serviceProvider->makeRegistrationProviders();

    }

}