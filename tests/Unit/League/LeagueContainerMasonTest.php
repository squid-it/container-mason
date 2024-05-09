<?php

declare(strict_types=1);

namespace SquidIT\Tests\Container\Mason\Unit\League;

use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SquidIT\Container\Mason\ContainerMasonInterface;
use SquidIT\Container\Mason\League\LeagueContainerMason;
use SquidIT\Tests\Container\Mason\Classes\Mailer;
use SquidIT\Tests\Container\Mason\Classes\UserManager;
use SquidIT\Tests\Container\Mason\Classes\UserManagerHeavy;

class LeagueContainerMasonTest extends TestCase
{
    private const string DEFINITION_FILE_NAME = 'LeagueDefinitions.php';

    private ContainerMasonInterface $leagueDiContainerMason;

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

        $definitionsArray             = require $filePath;
        $this->leagueDiContainerMason = new LeagueContainerMason($definitionsArray);
    }

    /**
     * @throws Exception
     */
    public function testGetNewContainerReturnsNewContainer(): void
    {
        $container1 = $this->leagueDiContainerMason->getNewContainer();
        $container2 = $this->leagueDiContainerMason->getNewContainer();

        self::assertNotSame($container1, $container2);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testGetNewReturnsMultipleNewObject(): void
    {
        $results = $this->leagueDiContainerMason->getNew(Mailer::class, UserManager::class);

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
        $mailer1     = $this->leagueDiContainerMason->get(Mailer::class);
        $mailer2     = $this->leagueDiContainerMason->get(Mailer::class);
        $mailer3     = $this->leagueDiContainerMason->getNew(Mailer::class)[Mailer::class];
        $userManager = $this->leagueDiContainerMason->getNew(UserManager::class)[UserManager::class];

        self::assertSame($this->leagueDiContainerMason->get(UserManager::class), $this->leagueDiContainerMason->get(UserManager::class));
        self::assertSame($mailer1, $mailer2); // multiple calls return the same object
        self::assertNotSame($mailer1, $mailer3); // get new returns a new object
        self::assertNotSame($userManager->mailer, $mailer1); // get new creates new dependency instances
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testGetReturnsSameObject(): void
    {
        $userManager1 = $this->leagueDiContainerMason->get(UserManager::class);
        $userManager2 = $this->leagueDiContainerMason->get(UserManager::class);

        self::assertSame($userManager1, $userManager2);
    }

    /**
     * @throws Exception
     */
    public function testHasReturnsCorrectBooleanValue(): void
    {
        self::assertTrue($this->leagueDiContainerMason->has(UserManager::class));
        self::assertFalse($this->leagueDiContainerMason->has('User'));
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
        $container                    = $this->leagueDiContainerMason->getNewContainer();
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
