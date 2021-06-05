<?php

namespace Codememory\Routing\Traits;

use Codememory\Routing\Route;
use Codememory\Routing\Software;
use Codememory\Routing\Utils;

/**
 * Trait RouteVerificationTrait
 * @package Codememory\Routing\Traits
 *
 * @author  Codememory
 */
trait RouteVerificationTrait
{

    /**
     * @param string $routePathRegex
     *
     * @return RouteVerificationTrait|Route
     */
    private function verifyByRoutePathRegex(string $routePathRegex): RouteVerificationTrait|Route
    {

        return $this->performVerification(function () use ($routePathRegex) {
            if (preg_match($routePathRegex, $this->request->url->getUrl())) {
                $this->statusVerifyRoute = true;
            } else {
                $this->statusVerifyRoute = false;
            }
        });

    }

    /**
     * @return RouteVerificationTrait|Route
     */
    private function verifyProtocol(): RouteVerificationTrait|Route
    {

        return $this->performVerification(function () {
            $schemes = array_map(function (string $protocol) {
                return $protocol . '://';
            }, $this->schemes);

            $this->statusVerifyRoute = in_array($this->request->url->getScheme(), $schemes);
        });

    }

    /**
     * @return RouteVerificationTrait|Route
     */
    private function verifyHeaders(): RouteVerificationTrait|Route
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
     * @return RouteVerificationTrait|Route
     */
    private function verifySoftware(Utils $utils): RouteVerificationTrait|Route
    {

        return $this->performVerification(function () use ($utils) {
            $software = new Software($this->request, $this->response, $utils, $this->getSoftware());

            $this->statusVerifyRoute = $software->make()->getSoftwareProcessingStatus();
        });

    }

    /**
     * @param callable $handler
     *
     * @return RouteVerificationTrait|Route
     */
    private function performVerification(callable $handler): RouteVerificationTrait|Route
    {

        if ($this->statusVerifyRoute || null === $this->statusVerifyRoute) {
            call_user_func($handler);
        }

        return $this;

    }

}