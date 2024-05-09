<?php

declare(strict_types=1);

namespace SquidIT\Tests\Container\Mason\Classes;

class Mailer
{
    public function mail(string $recipient, string $content): bool
    {
        // email the recipient
        return true;
    }
}
