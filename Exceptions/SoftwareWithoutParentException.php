<?php

namespace Codememory\Routing\Exceptions;

use Codememory\Routing\SoftwareAbstract;
use JetBrains\PhpStorm\Pure;

/**
 * Class SoftwareWithoutParentException
 * @package Codememory\Routing\Exceptions
 *
 * @author  Codememory
 */
class SoftwareWithoutParentException extends RoutingException
{

    /**
     * SoftwareWithoutParentException constructor.
     *
     * @param string $softwareNamespace
     */
    #[Pure]
    public function __construct(string $softwareNamespace)
    {

        parent::__construct(sprintf('The %s software must inherit the abstract class %s', $softwareNamespace, SoftwareAbstract::class));

    }

}