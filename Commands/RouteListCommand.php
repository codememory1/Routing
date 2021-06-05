<?php

namespace Codememory\Routing\Commands;

use Codememory\Components\Console\Command;
use Codememory\HttpFoundation\Request\Request;
use Codememory\Routing\Router;
use Codememory\Support\Arr;
use Codememory\Support\Str;
use Exception;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RouteListCommand
 * @package Codemememory\Routing\Commands
 *
 * @author  Codememory
 */
class RouteListCommand extends Command
{

    private const NUMBER_ROUTES = 10;

    /**
     * @var string|null
     */
    protected ?string $command = 'route:list';

    /**
     * @var string|null
     */
    protected ?string $description = 'Get a list of all routes as a table';

    /**
     * @return Command
     */
    protected function wrapArgsAndOptions(): Command
    {

        $this
            ->option('method', null, InputOption::VALUE_REQUIRED, sprintf(
                'Select routes by method, You can specify several methods by separating them with the (%s)',
                $this->tags->yellowText(',')
            ))
            ->option('page', null, InputOption::VALUE_REQUIRED, 'Open a specific page with routes', 1)
            ->option('number', null, InputOption::VALUE_REQUIRED, 'Display the number of routes on one page of the table', self::NUMBER_ROUTES);

        return $this;

    }

    /**
     * @inheritdoc
     *
     * @throws Exception
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        Router::__constructStatic(new Request());
        Router::initializingRoutesFromConfig();

        $inputMethods = $input->getOption('method');
        $methods = $inputMethods ? explode(',', $input->getOption('method')) : [];
        $methods = array_map(fn (mixed $method) => Str::toUppercase($method), $methods);
        $routeDataArray = $this->getRouteDataArray(Router::allRoutes(), $methods);
        $markedRoutes = $this->getMarkedRouteData($routeDataArray);

        [$openedPage, $totalPages, $routeDataForTable] = $this->getRoutesBySpace($input, $markedRoutes);

        $this->io->newLine();
        $this->io->writeln(Str::repeat('_', 50));
        $this->io->newLine();
        $this->io->writeln('Below is a table of routes');
        $this->io->writeln(sprintf('With the help of %s you can manage this table.', $this->tags->yellowText('options')));
        $this->io->newLine();
        $this->io->writeln(sprintf(
            '%s=%s outputs 2 routes per page of the table',
            $this->tags->greenText('--number'),
            $this->tags->yellowText('1')
        ));
        $this->io->writeln(sprintf(
            '%s=%s display routes with GET or POST methods',
            $this->tags->greenText('--method'),
            $this->tags->yellowText('get,post')
        ));
        $this->io->writeln(sprintf(
            '%s=%s open the second page of the table',
            $this->tags->greenText('--page'),
            $this->tags->yellowText('2')
        ));
        $this->io->writeln(Str::repeat('_', 50));

        $this->io->writeln($this->tags->whiteText(
            sprintf(
                "\n\n Total routes: %s\n",
                $this->tags->blueText(count($routeDataForTable))
            )
        ));

        $routeDataForTable = Arr::addAfterEach($routeDataForTable, new TableSeparator());
        array_pop($routeDataForTable);

        $table = new Table($output);
        $table
            ->setFooterTitle(sprintf('page %s/%s', $openedPage, $totalPages))
            ->setHeaders(['Scheme', 'URL', 'Method', 'Handler', 'Name', 'Parameters', 'Software'])
            ->setRows($routeDataForTable)->render();

        return Command::SUCCESS;

    }

    /**
     * @param array $allRoutes
     * @param array $methods
     *
     * @return array
     */
    private function getRouteDataArray(array $allRoutes, array $methods): array
    {

        $routeDataArray = [];

        foreach ($allRoutes as $route) {
            $routeHandler = $route->getResources()->getAction();

            $routeDataArray[] = [
                'scheme'     => $route->getSchemes(),
                'url'        => $route->getResources()->getPathGenerator()->getPath(),
                'method'     => $route->getMethod(),
                'handler'    => is_array($routeHandler) ? $routeHandler : 'Closure',
                'name'       => $route->getName(),
                'parameters' => $route->getRequiredParameters(),
                'software'   => $route->getSoftware()
            ];
        }

        if([] !== $methods) {
            foreach ($routeDataArray as $index => $routeData) {
                if (!in_array($routeData['method'], $methods)) {
                    unset($routeDataArray[$index]);
                }
            }
        }

        return $routeDataArray;

    }

    /**
     * @param array $routeDataArray
     *
     * @return array
     */
    private function getMarkedRouteData(array $routeDataArray): array
    {

        $markedRoutes = [];

        foreach ($routeDataArray as $routeData) {
            $scheme = array_map(function (string $scheme) {
                return $this->tags->yellowText($scheme);
            }, $routeData['scheme']);
            $routeHandler = $routeData['handler'];
            $parametersToString = null;
            $softwareToString = null;

            foreach ($routeData['parameters'] as $name => $value) {
                $parametersToString .= sprintf("%s = %s\n", $this->tags->redText($name), $this->tags->blueText($value));
            }

            foreach ($routeData['software'] as $namespace) {
                $softwareToString .= sprintf("%s\n", $namespace);
            }

            $markedRoutes[] = [
                implode('|', $scheme),
                $routeData['url'],
                $this->tags->redText($routeData['method']),
                is_array($routeHandler) ? sprintf('%s#%s', $this->tags->yellowText($routeHandler['class']), $this->tags->yellowText($routeHandler['call'])) : $this->tags->blueText($routeHandler),
                null !== $routeData['name'] ? $this->tags->redText($routeData['name']) : null,
                mb_substr($parametersToString, 0, -1),
                $softwareToString
            ];
        }

        return $markedRoutes;

    }

    /**
     * @param InputInterface $input
     * @param array          $markedRoutes
     *
     * @return array
     */
    private function getRoutesBySpace(InputInterface $input, array $markedRoutes): array
    {

        $routeDataForTable = [];
        $numberRoutes = (int) $input->getOption('number');
        $numberRoutes = $numberRoutes < 1 ? 1 : $numberRoutes;
        $totalRoutes = count($markedRoutes);
        $totalPages = ceil($totalRoutes / $numberRoutes);
        $openedPage = $this->getOpenedPage($input, $totalPages);
        $from = ($openedPage - 1) * $numberRoutes;
        $before = $from + $numberRoutes - 1;

        for ($i = $from < 0 ? 0 : $from; $i <= $before; $i++) {
            if (array_key_exists($i, $markedRoutes)) {
                $routeDataForTable[] = $markedRoutes[$i];
            }
        }

        return [$openedPage, $totalPages, $routeDataForTable];

    }

    /**
     * @param InputInterface $input
     * @param int            $totalPages
     *
     * @return int
     */
    private function getOpenedPage(InputInterface $input, int $totalPages): int
    {

        $openedPage = (int) $input->getOption('page');

        if ($openedPage < 1) {
            $openedPage = 1;
        }

        if ($openedPage > $totalPages) {
            $openedPage = $totalPages;
        }

        return $openedPage;

    }

}