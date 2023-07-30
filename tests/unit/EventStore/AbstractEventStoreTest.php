<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcing\Tests\Unit\EventStore;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use TwanHaverkamp\EventSourcing\AggregateRoot\AggregateRootInterface;
use TwanHaverkamp\EventSourcing\AggregateRoot\Example\ExampleAggregateRoot;
use TwanHaverkamp\EventSourcing\Event\EventCollection;
use TwanHaverkamp\EventSourcing\EventStore\Example\ExampleEventStore;
use TwanHaverkamp\EventSourcing\Uuid\Uuid;

/**
 * @coversDefaultClass \TwanHaverkamp\EventSourcing\EventStore\AbstractEventStore
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class AbstractEventStoreTest extends TestCase
{
    private static string $dir;

    public static function setUpBeforeClass(): void
    {
        self::$dir = __DIR__ . '/../../../var/event-store';
    }

    public static function tearDownAfterClass(): void
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(self::$dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($files as $file) {
            assert($file instanceof SplFileInfo);

            if ($file->getRealPath() !== false) {
                if ($file->isDir() === true) {
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
        }

        rmdir(self::$dir);
    }

    /**
     * @covers ::load
     */
    public function testLoadReturnsReconstitutedAggregateRoot(): void
    {
        $aggregateRoot = self::getAggregateRoot();

        $eventStore = new ExampleEventStore(self::$dir);
        $eventStore->save($aggregateRoot);

        $aggregateRootId = $aggregateRoot->getId();
        unset($aggregateRoot);

        $aggregateRoot = $eventStore->load($aggregateRootId, ExampleAggregateRoot::class);
        $projection = $aggregateRoot->getProjection();

        self::assertEquals('changed-required-value', $projection->requiredValue ?? null);
        self::assertEquals('changed-optional-value', $projection->optionalValue ?? null);
    }

    /**
     * Assertions:
     * - The aggregate root has no unsaved events before the save call.
     * - Theirs no subdirectory created for the aggregate root.
     *
     * @covers ::save
     */
    public function testSaveWitAggregateRootWithoutUnsavedEventsDoesntCreateSubdirectory(): void
    {
        $aggregateRoot = ExampleAggregateRoot::reconstitute(
            Uuid::init(),
            new EventCollection()
        );

        $eventStore = new ExampleEventStore(self::$dir);
        $eventStore->save($aggregateRoot);

        self::assertFalse($aggregateRoot->hasUnsavedEvents());
        self::assertDirectoryDoesNotExist(sprintf(
            '%s/%s',
            self::$dir,
            $aggregateRoot->getId()->toString(),
        ));
    }

    /**
     * Assertions:
     * - A subdirectory is created for the aggregate root.
     * - The amount of files in the aggregate root specific directory equals the amount of unsaved events.
     *
     * @covers ::save
     */
    public function testSaveWithAggregateRootWithUnsavedEventsCreatesSubdirectoryWithFiles(): void
    {
        $aggregateRoot = self::getAggregateRoot();

        $eventStore = new ExampleEventStore(self::$dir);
        $eventStore->save($aggregateRoot);

        $dir = sprintf(
            '%s/%s',
            self::$dir,
            $aggregateRoot->getId()->toString(),
        );

        self::assertDirectoryExists($dir);
        self::assertCount(3, glob("$dir/*.tmp") ?: []);
    }

    /**
     * Assertions:
     * - The aggregate root has unsaved events before the save call.
     * - The aggregate root has no unsaved events after the save call.
     *
     * @covers ::save
     */
    public function testSaveWithAggregateRootWithUnsavedEventsMarksAllEventsAsSaved(): void
    {
        $aggregateRoot = self::getAggregateRoot();
        self::assertCount(3, $aggregateRoot->getUnsavedEvents());

        $eventStore = new ExampleEventStore(self::$dir);
        $eventStore->save($aggregateRoot);

        $dir = sprintf(
            '%s/%s',
            self::$dir,
            $aggregateRoot->getId()->toString(),
        );

        self::assertCount(0, $aggregateRoot->getUnsavedEvents());
    }

    private static function getAggregateRoot(): AggregateRootInterface
    {
        $aggregateRoot = ExampleAggregateRoot::create(
            'init-required-value',
            'init-optional-value'
        );
        $aggregateRoot->changeRequiredValue('changed-required-value');
        $aggregateRoot->changeOptionalValue('changed-optional-value');

        return $aggregateRoot;
    }
}
