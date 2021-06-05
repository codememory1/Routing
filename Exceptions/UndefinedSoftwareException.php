<?php

namespace Codememory\Routing\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class UndefinedSoftwareException
 * @package Codememory\Routing\Exceptions
 *
 * @author  Codememory
 */
class UndefinedSoftwareException extends RoutingException
{

    /**
     * UndefinedSoftwareException constructor.
     *
     * @param string $softwareNamespace
     */
    #[Pure]
    public function __construct(string $softwareNamespace)
    {

        parent::__construct(sprintf('Undefined software %s', $softwareNamespace));

    }

}