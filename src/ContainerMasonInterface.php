<?php

declare(strict_types=1);

namespace SquidIT\Container\Mason;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

interface ContainerMasonInterface extends ContainerInterface
{
    /**
     * Find an entry of the container by its identifier and returns it.
     * The returned entry will always be the same object instance/value
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     *
     * @return mixed Entry.
     */
    public function get(string $id): mixed;

    /**
     * Find entry inside the container using identifier and always return a new instance.
     * All dependencies of the requested identifier will be resolved again to create new instances of all dependencies
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     *
     * @return mixed Entry.
     */
    public function getNew(string $id): mixed;

    /**
     * Find entries inside the container using identifiers and always return an array with new instances.
     *
     * @param string ...$ids Identifiers of the entries to look for.
     *
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     *
     * @return array<string, mixed> array key will be the id of the requested entry.
     */
    public function getNewMulti(string ...$ids): array;

    /**
     * Always returns a new container instance
     */
    public function getNewContainer(): ContainerInterface;
}
