<?php

namespace Codememory\Routing\Interfaces;

/**
 * Interface SoftwareLieHandlerInterface
 * @package Codememory\Routing\Interfaces
 *
 * @author  Codememory
 */
interface SoftwareLieHandlerInterface
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The method accepts a callback in which some software action
     * can be performed if it returns false
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param callable $callback
     *
     * @return bool
     */
    public function process(callable $callback): bool;

}