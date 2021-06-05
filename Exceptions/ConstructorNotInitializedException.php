<?php

namespace Codememory\Routing\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class ConstructorNotInitializedException
 * @package Codememory\Routing\Exceptions
 *
 * @author  Codememory
 */
class ConstructorNotInitializedException extends RoutingException
{

    /**
     * ConstructorNotInitializedException constructor.
     *
     * @param string $methodName
     */
    #[Pure]
    public function __construct(string $methodName)
    {

        parent::__construct(sprintf('Custom %s constructor not initialized', $methodName));

    }

}