<?php

namespace Codememory\Routing;

use Codememory\HttpFoundation\Client\Url;
use Codememory\Routing\Interfaces\ParametersInterface;
use Codememory\Routing\Interfaces\PathGeneratorInterface;

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
     * OutputParameters constructor.
     *
     * @param PathGeneratorInterface $pathGenerator
     * @param Url                    $url
     * @param array                  $expectedParameters
     */
    public function __construct(PathGeneratorInterface $pathGenerator, Url $url, array $expectedParameters)
    {

        $this->pathGenerator = $pathGenerator;
        $this->url = $url;
        $this->expectedParameters = $expectedParameters;

    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {

        preg_match($this->pathGenerator->getRegexPath($this->expectedParameters), $this->url->getUrl(), $match);

        foreach ($match as $name => $value) {
            if (is_int($name)) {
                unset($match[$name]);
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