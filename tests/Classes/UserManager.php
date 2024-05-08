<?php

declare(strict_types=1);

namespace SquidIT\Test\Container\Mason\Classes;

use RuntimeException;

class UserManager
{
    public function __construct(
        public Mailer $mailer
    ) {}

    public function register(string $email, string $password): void
    {
        // The user just registered, we create his account
        // ...

        // We email him to say hello!
        $result = $this->mailer->mail($email, 'Hello and welcome!');

        if ($result === true) {
            throw new RuntimeException('Unable to send welcome email');
        }
    }
}
