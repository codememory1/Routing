<?php

namespace Codememory\Routing;

use Codememory\HttpFoundation\Client\Url;
use Codememory\Routing\Interfaces\ParametersInterface;
use Codememory\Routing\Interfaces\PathGeneratorInterface;
use Codememory\Support\ConvertType;
use JetBrains\PhpStorm\Pure;

/**
 * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
 * Using this class, you can get the values of the route
 * parameters that are specified in the url address
 * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
 *
 * Class OutputParameters
 * @package Codememory\Routing
 *
 * @author  Codememory
 */
class OutputParameters implements ParametersInterface
{

    /**
     * @var PathGeneratorInterface
     */
    private PathGeneratorInterface $pathGenerator;

    /**
     * @var Url
     */
    private Url $url;

    /**
     * @var array
     */
    private array $expectedParameters;

    /**
     * @var ConvertType
     */
    private ConvertType $convertType;

    /**
     * OutputParameters constructor.
     *
     * @param PathGeneratorInterface $pathGenerator
     * @param Url                    $url
     * @param array                  $expectedParameters
     */
    #[Pure]
    public function __construct(PathGeneratorInterface $pathGenerator, Url $url, array $expectedParameters)
    {

        $this->pathGenerator = $pathGenerator;
        $this->url = $url;
        $this->expectedParameters = $expectedParameters;
        $this->convertType = new ConvertType();

    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {

        preg_match(
            $this->pathGenerator->getRegexPath($this->expectedParameters),
            $this->url->removeParameters($this->url->getUrl()),
            $match
        );

        foreach ($match as $name => &$value) {
            if (is_int($name)) {
                unset($match[$name]);
            } else {
                $value = $this->convertType->auto($value);
            }
        }

        return $match;

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