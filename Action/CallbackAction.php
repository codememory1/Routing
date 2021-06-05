<?php

namespace Codememory\Routing\Action;

use Codememory\Container\DependencyInjection\Interfaces\DependencyInjectionInterface;
use Codememory\Container\DependencyInjection\Interfaces\InjectionInterface;
use Exception;

/**
 * Class CallbackAction
 * @package Codememory\Routing\Action
 *
 * @author  Codememory
 */
class CallbackAction extends ActionAbstract
{

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function call(DependencyInjectionInterface $dependencyInjection): mixed
    {

        $id = $this->composeAndGetDependencyId();

        $dependencyInjection->add($id, $this->action, function (InjectionInterface $injection) {
            $injection->callback($this->outputParameters->all(), true);
        });

        return $dependencyInjection->get($id);

    }

}