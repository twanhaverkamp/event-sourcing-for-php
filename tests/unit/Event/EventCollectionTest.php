<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingForPhp\Tests\Unit\Event;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use TwanHaverkamp\EventSourcingForPhp\Event\EventCollection;
use TwanHaverkamp\EventSourcingForPhp\Event\EventInterface;
use TwanHaverkamp\EventSourcingForPhp\Event\Example\ExampleOptionalValueWasChangedEvent;
use TwanHaverkamp\EventSourcingForPhp\Event\Example\ExampleRequiredValueWasChangedEvent;
use TwanHaverkamp\EventSourcingForPhp\Event\Example\ExampleWasCreatedEvent;
use TwanHaverkamp\EventSourcingForPhp\Uuid\Uuid;

/**
 * @coversDefaultClass \TwanHaverkamp\EventSourcingForPhp\Event\EventCollection
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class EventCollectionTest extends TestCase
{
    /**
     * @covers ::has
     */
    public function testHasReturnsTrueIfCollectionContainsEvent(): void
    {
        $collection = (new EventCollection())
            ->add($event = self::getEvent());

        self::assertTrue($collection->has($event));
    }

    /**
     * @covers ::has
     */
    public function testHasReturnsFalseIfCollectionDoesntContainEvent(): void
    {
        $collection = (new EventCollection())
            ->add($event = self::getEvent());

        self::assertFalse($collection->has(clone $event));
    }

    /**
     * @covers ::has
     */
    public function testHasReturnsFalseIfCollectionHasNoEvents(): void
    {
        $collection = new EventCollection();

        self::assertFalse($collection->has(self::getEvent()));
    }

    /**
     * @covers ::add
     */
    public function testAddWithSingleEventReturnsCollectionWithSingleEvent(): void
    {
        $collection = (new EventCollection())
            ->add(self::getEvent());

        self::assertCount(1, $collection);
    }

    /**
     * @covers ::add
     */
    public function testAddWithDuplicateEventsReturnsCollectionWithSingleEvent(): void
    {
        $event = self::getEvent();
        $collection = (new EventCollection())
            ->add($event)
            ->add($event);

        self::assertCount(1, $collection);
    }

    /**
     * @covers ::add
     */
    public function testAddWithMultipleEventsReturnsCollectionWithAllEvents(): void
    {
        $collection = (new EventCollection())
            ->add($event = self::getEvent())
            ->add(clone $event);

        self::assertCount(2, $collection);
    }

    /**
     * @covers ::add
     */
    public function testAddWithMultipleEventsReturnsCollectionSortedByRegisteredAtAscending(): void
    {
        $aggregateRootId = Uuid::init();
        $recordedAt = new DateTimeImmutable();

        // The events are "randomly" added despite their "recordedAt" value.
        $collection = (new EventCollection())
            ->add($event1 = new ExampleOptionalValueWasChangedEvent(
                null,
                $aggregateRootId,
                $recordedAt,
            ))
            ->add($event3 = new ExampleRequiredValueWasChangedEvent(
                'changed-value-1',
                $aggregateRootId,
                (clone $recordedAt)->modify('-1 millisecond'),
            ))
            ->add($event2 = new ExampleOptionalValueWasChangedEvent(
                'changed-optional-value',
                $aggregateRootId,
                (clone $recordedAt)->modify('-1 microsecond'),
            ))
            ->add($event4 = new ExampleWasCreatedEvent(
                'init-value-1',
                'init-optional-value',
                $aggregateRootId,
                (clone $recordedAt)->modify('-1 second'),
            ));

        self::assertEquals($event4, $collection->current());

        $collection->next();
        self::assertEquals($event3, $collection->current());

        $collection->next();
        self::assertEquals($event2, $collection->current());

        $collection->next();
        self::assertEquals($event1, $collection->current());
    }

    /**
     * Assertions:
     * - The collection initially has 1 event.
     * - The collection has after removing no events.
     *
     * @covers ::remove
     */
    public function testRemoveWithSingleEventReturnsCollectionWithoutEvents(): void
    {
        $collection = (new EventCollection())
            ->add($event = self::getEvent());

        self::assertCount(1, $collection);

        $collection->remove($event);

        self::assertCount(0, $collection);
    }

    /**
     * Assertions:
     * - The collection initially has 2 events.
     * - The collection has after removing 1 remaining event.
     * - The collection has after removing the expected remaining event.
     *
     * @covers ::remove
     */
    public function testRemoveWithMultipleEventsReturnsCollectionWithExpectedRemainingEvent(): void
    {
        $collection = (new EventCollection())
            ->add($event = self::getEvent())
            ->add($clone = clone $event);

        self::assertCount(2, $collection);

        $collection->remove($event);

        self::assertCount(1, $collection);
        self::assertTrue($collection->has($clone));
    }

    /**
     * @dataProvider getCollectionAndExpectedReturnValueForCount
     *
     * @covers ::count
     */
    public function testCountReturnsExpectedValue(EventCollection $collection, int $expectedReturnValue): void
    {
        $actualReturnValue = $collection->count();

        self::assertSame($expectedReturnValue, $actualReturnValue);
    }

    /**
     * @covers ::current
     */
    public function testCurrentWithoutEventsReturnsFalse(): void
    {
        $collection = new EventCollection();

        self::assertFalse($collection->current());
    }

    /**
     * @covers ::current
     */
    public function testCurrentWithSingleEventReturnsEvent(): void
    {
        $collection = (new EventCollection())
            ->add(self::getEvent());

        self::assertInstanceOf(EventInterface::class, $collection->current());
    }

    /**
     * @covers ::current
     */
    public function testCurrentWithMultipleEventsReturnsExpectedEvent(): void
    {
        $aggregateRootId = Uuid::init();
        $recordedAt = new DateTimeImmutable();

        $collection = (new EventCollection())
            ->add(new ExampleRequiredValueWasChangedEvent(
                'changed-value-1',
                $aggregateRootId,
                $recordedAt,
            ))
            ->add($expectedEvent = new ExampleWasCreatedEvent(
                'init-value-1',
                'init-optional-value',
                $aggregateRootId,
                (clone $recordedAt)->modify('-1 microsecond'),
            ));

        self::assertSame($expectedEvent, $collection->current());
    }

    /**
     * @covers ::next
     * @covers ::key
     */
    public function testNextResultsInIncrementedKey(): void
    {
        $collection = new EventCollection();

        self::assertSame(0, $collection->key());

        for ($i = 1; $i <= 5; $i++) {
            $collection->next();

            self::assertSame($i, $collection->key());
        }
    }

    /**
     * @covers ::valid
     */
    public function testValidWhereKeyHasEventReturnsTrue(): void
    {
        $collection = (new EventCollection())
            ->add(self::getEvent());

        self::assertTrue($collection->valid());
    }

    /**
     * @covers ::valid
     */
    public function testValidWhereKeyHasNoEventReturnsFalse(): void
    {
        $collection = (new EventCollection())
            ->add(self::getEvent());

        $collection->next();
        self::assertFalse($collection->valid());
    }

    /**
     * @covers ::valid
     */
    public function testValidOnEmptyCollectionReturnsFalse(): void
    {
        $collection = new EventCollection();

        self::assertFalse($collection->valid());
    }

    /**
     * @covers ::rewind
     */
    public function testRewindResultsInKeyWithExpectedValue(): void
    {
        $collection = new EventCollection();

        for ($i = 1; $i <= 5; $i++) {
            $collection->next();
        }

        $collection->rewind();
        self::assertSame(0, $collection->key());
    }

    /**
     * Returns a {@see EventCollection} and the expected {@see EventCollection::count()} return value
     * for {@see testCountReturnsExpectedValue}.
     *
     * @return iterable<non-empty-array<string, mixed>>
     */
    public static function getCollectionAndExpectedReturnValueForCount(): iterable
    {
        $event = self::getEvent();
        $collection = new EventCollection();

        yield 'Empty collection' => [
            'collection' => $collection,
            'expectedReturnValue' => 0,
        ];

        $singleEventCollection = (clone $collection)
            ->add($event);

        yield 'Single event collection' => [
            'collection' => $singleEventCollection,
            'expectedReturnValue' => 1,
        ];

        $multipleEventCollection = (clone $collection)
            ->add($event)
            ->add(clone $event);

        yield 'Multiple event collection' => [
            'collection' => $multipleEventCollection,
            'expectedReturnValue' => 2,
        ];
    }

    private static function getEvent(): EventInterface
    {
        return new ExampleWasCreatedEvent(
            'example-required-value',
            'example-optional-value',
            Uuid::init(),
            new DateTimeImmutable(),
        );
    }
}
