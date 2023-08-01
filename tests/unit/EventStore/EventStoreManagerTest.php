<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingForPhp\Tests\Unit\EventStore;

use PHPUnit\Framework\TestCase;
use TwanHaverkamp\EventSourcingForPhp\AggregateRoot\AggregateRootInterface;
use TwanHaverkamp\EventSourcingForPhp\EventStore\EventStoreManager;
use TwanHaverkamp\EventSourcingForPhp\EventStore\Example\ExampleEventStore;
use TwanHaverkamp\EventSourcingForPhp\Exception\EventStoreNotFoundException;
use TwanHaverkamp\EventSourcingForPhp\Tests\Unit\EventStore\Mock\AggregateRootMock;
use TwanHaverkamp\EventSourcingForPhp\Tests\Unit\EventStore\Mock\EventStoreMock;

/**
 * @coversDefaultClass \TwanHaverkamp\EventSourcingForPhp\EventStore\EventStoreManager
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class EventStoreManagerTest extends TestCase
{
    /**
     * @dataProvider getAggregateRoot
     *
     * @covers ::load
     */
    public function testLoadWithAggregateRootWithAvailableEventStoreCallsLoadMethod(
        AggregateRootInterface $aggregateRoot
    ): void {
        $eventStoreMock = $this->createMock(EventStoreMock::class);
        $eventStoreMock
            ->expects(self::once())
            ->method('getClassName')
            ->willReturn(EventStoreMock::class);
        $eventStoreMock
            ->expects(self::once())
            ->method('load')
            ->with($aggregateRoot->getId(), $aggregateRoot::class)
            ->willReturn($aggregateRoot);

        $eventStoreManager = new EventStoreManager([
            $eventStoreMock,
            new ExampleEventStore('example-dir'),
        ]);

        $eventStoreManager->load($aggregateRoot->getId(), $aggregateRoot::class);
    }

    /**
     * @dataProvider getAggregateRoot
     *
     * @covers ::load
     */
    public function testLoadWithAggregateRootWithUnavailableEventStoreThrowsEventStoreNotFoundException(
        AggregateRootInterface $aggregateRoot
    ): void {
        $this->expectException(EventStoreNotFoundException::class);

        $eventStoreManager = new EventStoreManager([
            new ExampleEventStore('example-dir'),
        ]);
        $eventStoreManager->load($aggregateRoot->getId(), $aggregateRoot::class);
    }

    /**
     * @dataProvider getAggregateRoot
     *
     * @covers ::save
     */
    public function testSaveWithAggregateRootWithAvailableEventStoreCallsSaveMethod(
        AggregateRootInterface $aggregateRoot
    ): void {
        $eventStoreMock = $this->createMock(EventStoreMock::class);
        $eventStoreMock
            ->expects(self::once())
            ->method('getClassName')
            ->willReturn(EventStoreMock::class);
        $eventStoreMock
            ->expects(self::once())
            ->method('save')
            ->with($aggregateRoot);

        $eventStoreManager = new EventStoreManager([
            $eventStoreMock,
            new ExampleEventStore('example-dir'),
        ]);

        $eventStoreManager->save($aggregateRoot);
    }

    /**
     * @dataProvider getAggregateRoot
     *
     * @covers ::save
     */
    public function testSaveWithAggregateRootWithUnavailableEventStoreThrowsEventStoreNotFoundException(
        AggregateRootInterface $aggregateRoot
    ): void {
        $this->expectException(EventStoreNotFoundException::class);

        $eventStoreManager = new EventStoreManager([
            new ExampleEventStore('example-dir'),
        ]);
        $eventStoreManager->load($aggregateRoot->getId(), $aggregateRoot::class);
    }

    /**
     * @return iterable<string, non-empty-array<AggregateRootInterface>>
     */
    public static function getAggregateRoot(): iterable
    {
        yield 'Aggregate root with mocked event store' => [
            AggregateRootMock::create(),
        ];
    }
}
