<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use SquidIT\Tests\Container\Mason\Classes\Mailer;

return [
    /**
     * Custom object creation logic
     */
    Mailer::class => static function (ContainerInterface $c) {
        return new Mailer();
    },
];
