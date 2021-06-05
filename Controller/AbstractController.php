<?php

namespace Codememory\Routing\Controller;

use Codememory\Container\ServiceProvider\Interfaces\ServiceProviderInterface;
use Codememory\Routing\Traits\RegisteringOverriddenProvidersTrait;

/**
 * Class AbstractController
 * @package Codememory\Routing\Controller
 *
 * @author  Codememory
 */
abstract class AbstractController
{

    use RegisteringOverriddenProvidersTrait;

    /**
     * @var ServiceProviderInterface
     */
    private ServiceProviderInterface $serviceProvider;

    /**
     * AbstractController constructor.
     *
     * @param ServiceProviderInterface $serviceProvider
     */
    public function __construct(ServiceProviderInterface $serviceProvider)
    {

        $this->serviceProvider = $serviceProvider;

        $this->registerProviders();

    }

    /**
     * @param string $provider
     *
     * @return object
     */
    protected function get(string $provider): object
    {

        return $this->serviceProvider->get($provider);

    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    protected function getParameter(string $name): mixed
    {

        $binds = $this->get('config')->binds();

        return $binds[$name] ?? null;

    }

    /**
     * @param string $name
     * @param mixed  $value
     * @param bool   $checkExist
     *
     * @return bool
     */
    protected function setParameter(string $name, mixed $value, bool $checkExist = true): bool
    {

        $binds = $this->get('config')->binds();

        if (array_key_exists($name, $binds) && $checkExist) {
            return false;
        }

        $this->get('config')->setBind($name, $value);

        return true;

    }

    /**
     * @param string $property
     *
     * @return object
     */
    public function __get(string $property): object
    {

        return $this->get($property);

    }

}