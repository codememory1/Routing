<?php

namespace Codememory\Routing;

use Codememory\HttpFoundation\Interfaces\RequestInterface;
use Codememory\Routing\Interfaces\SoftwareLieHandlerInterface;

/**
 * Class SoftwareAbstract
 * @package Codememory\Routing
 *
 * @author  Codememory
 */
abstract class SoftwareAbstract
{

    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var SoftwareLieHandlerInterface
     */
    protected SoftwareLieHandlerInterface $softwareLieHandler;

    /**
     * SoftwareAbstract constructor.
     *
     * @param RequestInterface            $request
     * @param SoftwareLieHandlerInterface $softwareLieHandler
     */
    public function __construct(RequestInterface $request, SoftwareLieHandlerInterface &$softwareLieHandler)
    {

        $this->request = $request;
        $this->softwareLieHandler = $softwareLieHandler;

    }

}