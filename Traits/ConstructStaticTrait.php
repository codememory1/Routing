<?php

namespace Codememory\Routing\Traits;

use Codememory\Components\Configuration\Exceptions\ConfigNotFoundException;
use Codememory\Components\Environment\Exceptions\EnvironmentVariableNotFoundException;
use Codememory\Components\Environment\Exceptions\IncorrectPathToEnviException;
use Codememory\Components\Environment\Exceptions\ParsingErrorException;
use Codememory\Components\Environment\Exceptions\VariableParsingErrorException;
use Codememory\FileSystem\File;
use Codememory\FileSystem\Interfaces\FileInterface;
use Codememory\HttpFoundation\Interfaces\RequestInterface;
use Codememory\HttpFoundation\Interfaces\ResponseInterface;
use Codememory\HttpFoundation\Response\Response;
use Codememory\Routing\Exceptions\ConstructorNotInitializedException;
use Codememory\Routing\Exceptions\SingleConstructorInitializationException;
use Codememory\Routing\Utils;

/**
 * Trait ConstructStaticTrait
 * @package Codememory\Routing\Traits
 *
 * @author  Codememory
 */
trait ConstructStaticTrait
{

    /**
     * @var bool
     */
    private static bool $constructorInitialization = false;

    /**
     * @var RequestInterface
     */
    private static RequestInterface $request;

    /**
     * @var ResponseInterface
     */
    private static ResponseInterface $response;

    /**
     * @var FileInterface
     */
    private static FileInterface $filesystem;

    /**
     * @var Utils
     */
    private static Utils $utils;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * A static constructor that must be called before using any
     * routing methods and must only be called once
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param RequestInterface $request
     *
     * @throws SingleConstructorInitializationException
     * @throws ConfigNotFoundException
     * @throws EnvironmentVariableNotFoundException
     * @throws IncorrectPathToEnviException
     * @throws ParsingErrorException
     * @throws VariableParsingErrorException
     */
    public static function __constructStatic(RequestInterface $request)
    {

        self::checkSingleConstructorInitialization();

        self::$constructorInitialization = true;

        self::$request = $request;
        self::$response = new Response(self::$request->header);
        self::$filesystem = new File();
        self::$utils = new Utils(self::$filesystem);

        self::scanningAndImportFilesWithRoutes();

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check if a static constructor has been initialized
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @throws ConstructorNotInitializedException
     */
    private static function checkConstructorInitialization(): void
    {

        if (false === self::$constructorInitialization) {
            throw new ConstructorNotInitializedException('__constructStatic');
        }

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check re-invocation of static constructor
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @throws SingleConstructorInitializationException
     */
    private static function checkSingleConstructorInitialization(): void
    {

        if (self::$constructorInitialization) {
            throw new SingleConstructorInitializationException('__constructStatic');
        }

    }

}