<?php

namespace Codememory\Routing\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class IncorrectControllerException
 * @package Codememory\Routing\Exceptions
 *
 * @author  Codememory
 */
class IncorrectControllerException extends RoutingException
{

    /**
     * IncorrectControllerException constructor.
     *
     * @param string|null $controller
     */
    #[Pure]
    public function __construct(?string $controller)
    {

        parent::__construct(sprintf('Invalid controller namespace. Controller "%s" not found', $controller));

    }

}