<?php

namespace Codememory\Routing;

use Codememory\Routing\Interfaces\ParametersInterface;

/**
 * Class InputParameters
 * @package Codememory\Routing
 *
 * @author  Codememory
 */
class InputParameters implements ParametersInterface
{

    public const DEFAULT_PARAMETER_REGEX = '.*';
    public const PARAMETER_START_CHARACTER = ':';
    private const PARAMETER_NAME_REGEX = '[a-zA-Z\_\-\.]{2,}';

    /**
     * @var string
     */
    private string $routePath;

    /**
     * InputParameters constructor.
     *
     * @param string $routePath
     */
    public function __construct(string $routePath)
    {

        $this->routePath = $routePath;

    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {

        $regex = sprintf('/\%s(?<parameters>%s)(?:\/|$)/', self::PARAMETER_START_CHARACTER, self::PARAMETER_NAME_REGEX);

        preg_match_all($regex, $this->routePath, $match);

        return $match['parameters'];

    }

    /**
     * @inheritDoc
     */
    public function getFirstParameter(): ?string
    {

        return $this->all()[array_key_first($this->all())] ?? null;

    }

    /**
     * @inheritDoc
     */
    public function getLastParameter(): ?string
    {

        return $this->all()[array_key_last($this->all())] ?? null;

    }

}