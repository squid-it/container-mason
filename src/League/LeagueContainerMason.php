<?php

declare(strict_types=1);

namespace SquidIT\Container\Mason\League;

use Exception;
use League\Container\Container;
use League\Container\Definition\Definition;
use League\Container\Definition\DefinitionAggregate;
use League\Container\ReflectionContainer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use SquidIT\Container\Mason\ContainerMasonInterface;
use UnexpectedValueException;

class LeagueContainerMason implements ContainerMasonInterface
{
    private ContainerInterface|Container $container;

    private readonly DefinitionAggregate $definitionAggregate;

    /**
     * @param array<int|string, Definition> $definitions
     *
     * @throws Exception
     */
    public function __construct(
        array $definitions
    ) {
        foreach ($definitions as $definition) {
            if (!($definition instanceof Definition)) {
                throw new UnexpectedValueException('Definitions must only contain instances of Definition.');
            }

            $definition->setShared(true);
        }

        $this->definitionAggregate = new DefinitionAggregate($definitions);
        $this->container           = $this->getNewContainer();
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
     */
    public function getNew(string $id): mixed
    {
        /** @phpstan-ignore-next-line */
        return $this->container->getNew($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getNewMulti(string ...$ids): array
    {
        $result = [];

        foreach ($ids as $id) {
            /** @phpstan-ignore-next-line */
            $result[$id] = $this->container->getNew($id);
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public function getNewContainer(): ContainerInterface
    {
        return $this->buildContainer();
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
        $container = new Container($this->definitionAggregate);
        $container->delegate(new ReflectionContainer(true));

        return $container;
    }
}
