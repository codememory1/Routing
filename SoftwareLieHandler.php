<?php

namespace Codememory\Routing;

use Codememory\Routing\Interfaces\SoftwareLieHandlerInterface;

/**
 * Class SoftwareLieHandler
 * @package Codememory\Routing
 *
 * @author  Codememory
 */
class SoftwareLieHandler implements SoftwareLieHandlerInterface
{

    /**
     * @var mixed
     */
    private mixed $handler = null;

    /**
     * @inheritDoc
     */
    public function process(callable $callback): bool
    {

        $this->handler = call_user_func($callback);

        return false;

    }

    /**
     * @return mixed
     */
    public function getProcess(): mixed
    {

        return $this->handler;

    }

}