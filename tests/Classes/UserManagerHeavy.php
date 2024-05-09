<?php

declare(strict_types=1);

namespace SquidIT\Tests\Container\Mason\Classes;

use Random\RandomException;

class UserManagerHeavy extends UserManager
{
    public const int INSTANTIATED_SIZE = 102400;

    protected string $randomBytes;

    /**
     * @throws RandomException
     */
    public function __construct(
        public Mailer $mailer
    ) {
        parent::__construct($mailer);

        $this->randomBytes = random_bytes(self::INSTANTIATED_SIZE);
    }
}
