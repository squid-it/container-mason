<?php

declare(strict_types=1);

namespace SquidIT\Container\Mason;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

interface ContainerMasonInterface extends ContainerInterface
{
    /**
     * Always returns a new container instance
     */
    public function getNewContainer(): ContainerInterface;

    /**
     * Find entries inside the container using identifiers and always return an array with new instances.
     *
     * @param string ...$ids Identifiers of the entries to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return array<string, mixed> array key will be the id of the requested entry.
     */
    public function getNew(string ...$ids): array;

    /**
     * Find an entry of the container by its identifier and returns it.
     * The returned entry will always be the same object instance/value
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws ContainerExceptionInterface Error while retrieving the entry.*@throws Exception
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws Exception
     *
     * @return mixed Entry.
     */
    public function get(string $id): mixed;
}
