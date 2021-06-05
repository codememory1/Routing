<?php

namespace Codememory\Routing\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class SingleConstructorInitializationException
 * @package Codememory\Routing\Exceptions
 *
 * @author  Codememory
 */
class SingleConstructorInitializationException extends RoutingException
{

    /**
     * SingleConstructorInitializationException constructor.
     *
     * @param string $methodName
     */
    #[Pure] public function __construct(string $methodName)
    {

        parent::__construct(sprintf('The %s method should only be called once', $methodName));

    }

}