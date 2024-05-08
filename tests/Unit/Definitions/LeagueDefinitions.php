<?php

declare(strict_types=1);

use League\Container\Definition\Definition;
use SquidIT\Test\Container\Mason\Classes\Mailer;

/**
 * Custom object creation logic can be created using definitions
 */
return [
    new Definition(Mailer::class),
];
