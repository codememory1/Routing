<?php

namespace Codememory\Routing\Action;

use Codememory\Components\Configuration\Exceptions\ConfigNotFoundException;
use Codememory\Components\Environment\Exceptions\EnvironmentVariableNotFoundException;
use Codememory\Components\Environment\Exceptions\IncorrectPathToEnviException;
use Codememory\Components\Environment\Exceptions\ParsingErrorException;
use Codememory\Components\Environment\Exceptions\VariableParsingErrorException;
use Codememory\Components\View\Interfaces\ViewInterface;
use Codememory\Container\DependencyInjection\Interfaces\DependencyInjectionInterface;
use Codememory\Container\DependencyInjection\Interfaces\InjectionInterface;
use Codememory\Container\ServiceProvider\ServiceProvider;
use Codememory\FileSystem\File;
use Codememory\Support\Str;
use Exception;
use JetBrains\PhpStorm\Pure;

/**
 * Class ControllerAction
 * @package Codememory\Routing\Action
 *
 * @author  Codememory
 */
class ControllerAction extends ActionAbstract
{

    public const NAMESPACE_AND_METHOD_SEPARATOR = '#';

    /**
     * @return string
     */
    public function getNamespace(): string
    {

        $namespace = Str::trimAfterSymbol($this->action, self::NAMESPACE_AND_METHOD_SEPARATOR);

        return str_replace('.', '\\', $namespace);

    }

    /**
     * @return string
     */
    #[Pure]
    public function getMethod(): string
    {

        return Str::trimToSymbol($this->action, self::NAMESPACE_AND_METHOD_SEPARATOR);

    }

    /**
     * @inheritDoc
     *
     * @return mixed
     * @throws ConfigNotFoundException
     * @throws EnvironmentVariableNotFoundException
     * @throws IncorrectPathToEnviException
     * @throws ParsingErrorException
     * @throws VariableParsingErrorException
     * @throws Exception
     */
    public function call(DependencyInjectionInterface $dependencyInjection): mixed
    {

        $serviceProvider = new ServiceProvider(new File());

        $id = $this->composeAndGetDependencyId();

        $dependencyInjection->add($id, $this->getNamespace(), function (InjectionInterface $injection) use ($serviceProvider) {
            $injection
                ->construct([$serviceProvider])
                ->method($this->getMethod(), $this->outputParameters->all(), true);
        });

        return $this->whenMethodReturnsView($dependencyInjection->get($id));

    }

    /**
     * @param mixed $controllerMethodReturn
     *
     * @return mixed
     * @throws ConfigNotFoundException
     * @throws EnvironmentVariableNotFoundException
     * @throws IncorrectPathToEnviException
     * @throws ParsingErrorException
     * @throws VariableParsingErrorException
     */
    private function whenMethodReturnsView(mixed $controllerMethodReturn): mixed
    {

        if ($controllerMethodReturn instanceof ViewInterface) {
            $controllerMethodReturn->makeOutput();

            return null;
        }

        return $controllerMethodReturn;

    }

}