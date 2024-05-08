<?php

declare(strict_types=1);

namespace SquidIT\Test\Container\Mason\Classes;

class Mailer
{
    public function mail(string $recipient, string $content): bool
    {
        // email the recipient
        return true;
    }
}
