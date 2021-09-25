<?php

namespace Codememory\Routing\Commands;

use Codememory\Components\Console\Command;
use Codememory\Components\JsonParser\Exceptions\JsonErrorException;
use Codememory\Components\JsonParser\JsonParser;
use Codememory\FileSystem\File;
use Codememory\FileSystem\Interfaces\FileInterface;
use Codememory\Routing\Utils;
use Codememory\Support\Arr;
use Codememory\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MakeSoftwareCommand
 *
 * @package Codememory\Routing\Commands
 *
 * @author  Codememory
 */
class MakeSoftwareCommand extends Command
{

    /**
     * @var string|null
     */
    protected ?string $command = 'make:software';

    /**
     * @var string|null
     */
    protected ?string $description = 'Create a software class';

    /**
     * @return Command
     */
    protected function wrapArgsAndOptions(): Command
    {

        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Class name for software')
            ->addArgument('method', InputArgument::REQUIRED, 'Creates a method in software')
            ->addOption('re-create', null, InputOption::VALUE_NONE, 'Recreate the software if the software being created already exists');

        return $this;

    }

    /**
     * @inheritDoc
     * @throws JsonErrorException
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        $filesystem = new File();
        $routerUtils = new Utils();

        $namespaceWithSoftware = rtrim($routerUtils->getBasicSettings()['softwareNamespace'], '\\') . '\\';
        $softwareName = $input->getArgument('name');
        $softwareNamespace = $namespaceWithSoftware . $softwareName;

        $softwarePath = Str::asPath($softwareNamespace . '.php', '\\');
        $softwarePath = $this->getCorrectPathToSoftware($filesystem, $softwarePath);

        if ($filesystem->exist($softwarePath) && !$input->getOption('re-create')) {
            $this->io->error([
                'The red software already exists.',
                'If you want to recreate, use the --re-create option'
            ]);

            return self::FAILURE;
        }

        $this->createSoftware(
            $filesystem,
            $namespaceWithSoftware,
            $softwareName,
            $input->getArgument('method'),
            $softwarePath
        );

        return self::SUCCESS;

    }

    /**
     * @param FileInterface $filesystem
     * @param string        $namespaceWithSoftware
     * @param string        $softwareName
     * @param string        $methodName
     * @param string        $softwarePath
     *
     * @return void
     */
    private function createSoftware(FileInterface $filesystem, string $namespaceWithSoftware, string $softwareName, string $methodName, string $softwarePath): void
    {

        $softwareStub = file_get_contents(__DIR__ . '/Stubs/SoftwareStub.stub');

        Str::replace($softwareStub, [
            '{namespace}', '{className}', '{methodName}'
        ], [
            Str::trimAfterSymbol($namespaceWithSoftware, '\\', false), $softwareName, $methodName
        ]);


        file_put_contents($filesystem->getRealPath($softwarePath), $softwareStub);

        $this->io->success([
            'Software successfully created',
            sprintf('Path: %s', $softwarePath)
        ]);

    }

    /**
     * @param FileInterface $filesystem
     * @param string|null   $softwarePath
     *
     * @return string|null
     * @throws JsonErrorException
     */
    private function getCorrectPathToSoftware(FileInterface $filesystem, ?string $softwarePath): ?string
    {

        $jsonParser = new JsonParser();

        $composer = $jsonParser->setData(
            file_get_contents($filesystem->getRealPath('/composer.json'))
        )->decode();
        $psr4 = Arr::set($composer)::get('autoload.psr-4') ?: [];

        foreach ($psr4 as $namespace => $path) {
            $namespaceInPath = str_replace('\\', '/', $namespace);

            if (Str::starts($softwarePath, $namespaceInPath)) {
                Str::replace($softwarePath, $namespaceInPath, $path);
            }
        }

        return $softwarePath;

    }

}