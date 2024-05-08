<?php

declare(strict_types=1);

namespace SquidIT\Test\Container\Mason\Unit\PhpDi;

use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SquidIT\Container\Mason\ContainerMasonInterface;
use SquidIT\Container\Mason\PhpDi\PhpDiContainerMason;
use SquidIT\Test\Container\Mason\Classes\Mailer;
use SquidIT\Test\Container\Mason\Classes\UserManager;
use SquidIT\Test\Container\Mason\Classes\UserManagerHeavy;

class PhpDiContainerMasonTest extends TestCase
{
    private const string DEFINITION_FILE_NAME = 'PhpDiDefinitions.php';

    private ContainerMasonInterface $phpDiContainerMason;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $filePath = sprintf(
            '%s%s..%sDefinitions%s%s',
            __DIR__,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            self::DEFINITION_FILE_NAME
        );

        $this->phpDiContainerMason = new PhpDiContainerMason([$filePath]);
    }

    /**
     * @throws Exception
     */
    public function testGetNewContainerReturnsNewContainer(): void
    {
        $container1 = $this->phpDiContainerMason->getNewContainer();
        $container2 = $this->phpDiContainerMason->getNewContainer();

        self::assertNotSame($container1, $container2);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testGetNewReturnsMultipleNewObject(): void
    {
        $results = $this->phpDiContainerMason->getNew(Mailer::class, UserManager::class);

        self::assertCount(2, $results);
        self::assertArrayHasKey(Mailer::class, $results);
        self::assertArrayHasKey(UserManager::class, $results);
        self::assertInstanceOf(Mailer::class, $results[Mailer::class]);
        self::assertInstanceOf(UserManager::class, $results[UserManager::class]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testGetNewReturnsNewObject(): void
    {
        $container = $this->phpDiContainerMason->getNewContainer();

        $mailer1 = $container->get(Mailer::class);
        $mailer2 = $container->get(Mailer::class);
        $mailer3 = $this->phpDiContainerMason->getNew(Mailer::class)[Mailer::class];

        /** @phpstan-ignore-next-line */
        $userManager = $container->make(UserManager::class);

        self::assertSame($mailer1, $mailer2); // multiple calls return the same object
        self::assertSame($userManager->mailer, $mailer1); // PHP-DI - make does not create new dependency instances
        self::assertNotSame($mailer1, $mailer3);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testGetReturnsSameObject(): void
    {
        $userManager1 = $this->phpDiContainerMason->get(UserManager::class);
        $userManager2 = $this->phpDiContainerMason->get(UserManager::class);

        self::assertSame($userManager1, $userManager2);
    }

    /**
     * @throws Exception
     */
    public function testHasReturnsCorrectBooleanValue(): void
    {
        self::assertTrue($this->phpDiContainerMason->has(UserManager::class));
        self::assertFalse($this->phpDiContainerMason->has('User'));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testContainerIsLazyLoadingObjects(): void
    {
        /**
         * UserManager::class, when instantiated consumes at least 100kb
         */
        $memoryBeforeLoadingContainer = memory_get_usage();
        $container                    = $this->phpDiContainerMason->getNewContainer();
        $memoryAfterLoadingContainer  = memory_get_usage();
        $containerMemoryConsumption   = $memoryAfterLoadingContainer - $memoryBeforeLoadingContainer;

        $userManger                    = $container->get(UserManagerHeavy::class);
        $memoryAfterLoadingUserManager = memory_get_usage() - $memoryAfterLoadingContainer;

        /**
         * After loading UserManager::class we should see an increase of at least 100KB in memory consumption
         */
        $memoryIncrease = $memoryAfterLoadingUserManager - $containerMemoryConsumption;
        self::assertGreaterThanOrEqual(UserManagerHeavy::INSTANTIATED_SIZE, $memoryIncrease);
    }
}
