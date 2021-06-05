<?php

namespace Codememory\Routing\Interfaces;

/**
 * Interface SoftwareDataInterface
 * @package Codememory\Routing\Interfaces
 *
 * @author  Codememory
 */
interface SoftwareDataInterface
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Retrieves the name of the software
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string
     */
    public function getSoftwareName(): string;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the method name from the software
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string|null
     */
    public function getSoftwareMethod(): ?string;

}