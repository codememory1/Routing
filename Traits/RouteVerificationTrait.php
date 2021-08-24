<?php

namespace Codememory\Routing\Traits;

use Codememory\Routing\Software;
use Codememory\Routing\Utils;

/**
 * Trait RouteVerificationTrait
 *
 * @package Codememory\Routing\Traits
 *
 * @author  Codememory
 */
trait RouteVerificationTrait
{

    /**
     * @param string $routePathRegex
     *
     * @return static
     */
    private function verifyByRoutePathRegex(string $routePathRegex): static
    {

        return $this->performVerification(function () use ($routePathRegex) {
            $url = $this->request->url->removeParameters($this->request->url->getUrl());

            if (preg_match($routePathRegex, $url)) {
                $this->statusVerifyRoute = true;
            } else {
                $this->statusVerifyRoute = false;
            }
        });

    }

    /**
     * @return static
     */
    private function verifyProtocol(): static
    {

        return $this->performVerification(function () {
            $schemes = array_map(function (string $protocol) {
                return $protocol . '://';
            }, $this->schemes);

            $this->statusVerifyRoute = in_array($this->request->url->getScheme(), $schemes);
        });

    }

    /**
     * @return static
     */
    private function verifyHeaders(): static
    {

        return $this->performVerification(function () {
            $responseHeaders = $this->request->header->getAll();
            $expectedHeaders = $this->resources->getHeaders();

            foreach ($expectedHeaders as $name => $value) {
                if ($this->request->hasHeader($name) && $responseHeaders[$name] === $value) {
                    $this->statusVerifyRoute = true;
                } else {
                    $this->statusVerifyRoute = false;
                }
            }
        });

    }

    /**
     * @param Utils $utils
     *
     * @return static
     */
    private function verifySoftware(Utils $utils): static
    {

        return $this->performVerification(function () use ($utils) {
            $software = new Software($this->request, $this->response, $utils, $this->getSoftware());

            $this->statusVerifyRoute = $software->make()->getSoftwareProcessingStatus();
        });

    }

    /**
     * @return static
     */
    private function verifySubdomain(): static
    {

        return $this->performVerification(function () {
            $this->statusVerifyRoute = $this->request->url->getSubdomain() === $this->getResources()->getSubdomain();
        });

    }

    /**
     * @param callable $handler
     *
     * @return static
     */
    private function performVerification(callable $handler): static
    {

        if ($this->statusVerifyRoute || null === $this->statusVerifyRoute) {
            call_user_func($handler);
        }

        return $this;

    }

}