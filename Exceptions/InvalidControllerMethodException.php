<?php

namespace Codememory\Routing\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class InvalidControllerMethodException
 * @package Codememory\Routing\Exceptions
 *
 * @author  Codememory
 */
class InvalidControllerMethodException extends RoutingException
{

    /**
     * InvalidControllerMethodException constructor.
     *
     * @param string|null $controller
     * @param string|null $method
     */
    #[Pure]
    public function __construct(?string $controller, ?string $method)
    {

        parent::__construct(sprintf('Invalid controller method. Method "%s" in controller "%s" is undefined', $method, $controller));

    }

}