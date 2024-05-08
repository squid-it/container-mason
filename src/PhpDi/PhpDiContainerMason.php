<?php

declare(strict_types=1);

namespace SquidIT\Container\Mason\PhpDi;

use DI\Container;
use DI\ContainerBuilder;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use SquidIT\Container\Mason\ContainerMasonInterface;

class PhpDiContainerMason implements ContainerMasonInterface
{
    /** @var array<int|string, mixed> */
    protected array $definitions;

    /** @var ContainerBuilder<Container> */
    private ContainerBuilder $containerBuilder;

    private ContainerInterface|Container $container;

    /**
     * @param array<string> $definitionFilePaths
     *
     * @throws Exception
     */
    public function __construct(
        private readonly array $definitionFilePaths
    ) {
        $definitionsArray = [];

        foreach ($this->definitionFilePaths as $definitionFilePath) {
            $definitionsArray[] = require $definitionFilePath;
        }

        $this->definitions = \array_merge(...$definitionsArray);
        $this->container   = $this->getNewContainer();
    }

    /**
     * @throws Exception
     */
    public function getNewContainer(): ContainerInterface
    {
        if (isset($this->containerBuilder)) {
            return $this->containerBuilder->build();
        }

        return $this->buildContainer();
    }

    /**
     * @throws ContainerExceptionInterface|Exception|NotFoundExceptionInterface
     *
     * @return array<string, mixed>
     */
    public function getNew(string ...$ids): array
    {
        /**
         * PHP-DI make() method only creates a new instance for the requested id
         * all dependencies of id will be fetched from the container and will reuse the same instances
         *
         * to fix this, we create a new container to force all dependencies to be new instances
         */
        $container = $this->getNewContainer();
        $result    = [];

        foreach ($ids as $id) {
            $result[$id] = $container->get($id);
        }

        unset($container);

        return $result;
    }

    /**
     * Find an entry of the container by its identifier and returns it.
     * The returned entry will always be the same object instance/value
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws Exception
     * @throws ContainerExceptionInterface Error while retrieving the entry.*@throws Exception
     *
     * @return mixed Entry.
     */
    public function get(string $id): mixed
    {
        return $this->container->get($id);
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function has(string $id): bool
    {
        return $this->container->has($id);
    }

    /**
     * @throws Exception
     */
    private function buildContainer(): ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions($this->definitions);

        $this->containerBuilder = $containerBuilder;

        return $containerBuilder->build();
    }
}
