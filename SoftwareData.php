<?php

namespace Codememory\Routing;

use Codememory\Routing\Interfaces\SoftwareDataInterface;
use JetBrains\PhpStorm\Pure;

/**
 * Class SoftwareData
 * @package Codememory\Routing
 *
 * @author  Codememory
 */
class SoftwareData implements SoftwareDataInterface
{

    /**
     * @var string
     */
    private string $software;

    /**
     * SoftwareData constructor.
     *
     * @param string $software
     */
    public function __construct(string $software)
    {

        $this->software = $software;

    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function getSoftwareName(): string
    {

        return $this->softwareData()[0];

    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function getSoftwareMethod(): ?string
    {

        return $this->softwareData()[1] ?? null;

    }

    /**
     * @return array
     */
    private function softwareData(): array
    {

        return explode(Software::DELIMITER_CHAR_METHOD_NAME, $this->software);

    }

}