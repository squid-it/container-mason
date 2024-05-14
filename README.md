# Container Mason

PSR-11 compatible Interface to unify the creation of new container instances and objects

## Why / Use case
Currently, there are many different PSR-11 container implementations available. Unfortunately, they do not always work
in the same way.

Depending on the container implementation or configuration, fetching an entry from the container can result in different 
instances of the requested object.

```php
<?php

$serviceClass1 = $container->get(Service::class);
$serviceClass2 = $container->get(Service::class);

if ($serviceClass1 === $serviceClass2) {
    echo 'We got the same instance';
} else {
    echo 'We got 2 different instances';
}
```

Forcing a new object instance is also not standardized by PSR-11. Depending on the container implementation, this could be 
a shallow new object instance, or a complete new instances with all new instantiated dependencies.

```php
<?php

$serviceClass1 = $container->make(Service::class);
$serviceClass2 = $container->build(Service::class);
$serviceClass3 = $container->getNew(Service::class);
```

### Coroutines
While implementing and working with coroutines, it is important to be in precise control of fetching objects from the container.
Or supplying a coroutine with its own isolated container (depending on the implementation).

That is the reason why I created this interface.

### Goal - Implementing the interface
When implementing this interface, make sure you follow the following rules
* *get()* method - MUST always return the same instance
* *getNew()* method - MUST always return a new instance with fresh dependencies (share nothing)
* *getNewMulti()* method - MUST work like getNew() but always returns an array with new instances as values and the requested id as array key
* *getNewContainer()* method - MUST always return a new container instance


## Installation

You can install this package using composer:

``` bash
composer require squidit/container-mason
```

This package does not require a specific DI container, you need to implement the provided interfaces and
include your own DI container.

Example Container: PHP-DI  
Link/Documentation: [PHP-DI 7](https://php-di.org/).
``` bash
composer require php-di/php-di
```

Some other PSR-11 container  

Example Container: The League of Extraordinary Packages - Container  
Link/Documentation: [Container The PHP League](https://container.thephpleague.com/).
``` bash
composer require league/container
```

## The interface
```php
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
```

### Examples

The source directory contains two folders
* League
* PhpDi

Both folders contain an example implementation.
