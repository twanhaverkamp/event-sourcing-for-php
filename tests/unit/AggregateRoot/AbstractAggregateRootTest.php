<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcing\Tests\Unit\AggregateRoot;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use TwanHaverkamp\EventSourcing\AggregateRoot\AbstractAggregateRoot;
use TwanHaverkamp\EventSourcing\AggregateRoot\AggregateRootInterface;
use TwanHaverkamp\EventSourcing\AggregateRoot\Example\ExampleAggregateRoot;
use TwanHaverkamp\EventSourcing\Event\EventCollection;
use TwanHaverkamp\EventSourcing\Event\EventInterface;
use TwanHaverkamp\EventSourcing\Event\Example\ExampleWasCreatedEvent;
use TwanHaverkamp\EventSourcing\Uuid\Uuid;

/**
 * @coversDefaultClass \TwanHaverkamp\EventSourcing\AggregateRoot\AbstractAggregateRoot
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class AbstractAggregateRootTest extends TestCase
{
    /**
     * @covers ::reconstitute
     */
    public function testReconstituteOnDummyReturnsExpectedAggregateRootWithExpectedUuidAndEventCollection(): void
    {
        $aggregateRootId = Uuid::init();
        $events = new EventCollection();

        $aggregateRoot = ExampleAggregateRoot::reconstitute($aggregateRootId, $events);
        self::assertInstanceOf(ExampleAggregateRoot::class, $aggregateRoot);

        $property = new ReflectionProperty(AbstractAggregateRoot::class, 'id');
        $property->setAccessible(true);
        self::assertEquals($aggregateRootId, $property->getValue($aggregateRoot));

        $property = new ReflectionProperty(AbstractAggregateRoot::class, 'events');
        $property->setAccessible(true);
        self::assertEquals($events, $property->getValue($aggregateRoot));
    }

    /**
     * @covers ::getId
     */
    public function testGetIdOnReconstitutedDummyReturnsExpectedUuid(): void
    {
        $aggregateRootId = Uuid::init();
        $aggregateRoot = ExampleAggregateRoot::reconstitute($aggregateRootId, new EventCollection());

        self::assertEquals($aggregateRootId, $aggregateRoot->getId());
    }

    /**
     * @covers ::getProjection
     */
    public function testGetProjectionOnCreatedDummyReturnsExpectedObjectWithExpectedPropertiesAndValues(): void
    {
        $requiredValue = 'example-required-value';
        $optionalValue = 'example-optional-value';

        $aggregateRoot = ExampleAggregateRoot::create($requiredValue, $optionalValue);
        $this->simulateSaveAction($aggregateRoot);

        $projection = $aggregateRoot->getProjection();

        self::assertEquals($requiredValue, $projection->requiredValue ?? null);
        self::assertEquals($optionalValue, $projection->optionalValue ?? null);
    }

    /**
     * @covers ::hasUnsavedEvents
     */
    public function testHasUnsavedEventsOnCreatedDummyReturnsTrue(): void
    {
        $aggregateRoot = ExampleAggregateRoot::create(
            'example-required-value',
            'example-optional-value',
        );

        self::assertTrue($aggregateRoot->hasUnsavedEvents());
    }

    /**
     * @covers ::hasUnsavedEvents
     */
    public function testHasUnsavedEventsOnReconstitutedDummyReturnsFalse(): void
    {
        $aggregateRootId = Uuid::init();
        $aggregateRoot = ExampleAggregateRoot::reconstitute($aggregateRootId, new EventCollection());

        self::assertFalse($aggregateRoot->hasUnsavedEvents());
    }

    /**
     * @covers ::getUnsavedEvents
     */
    public function testGetUnsavedEventsOnCreatedDummyReturnsExpectedEvent(): void
    {
        $aggregateRoot = ExampleAggregateRoot::create(
            'example-required-value',
            'example-optional-value',
        );
        $events = $aggregateRoot->getUnsavedEvents();

        self::assertCount(1, $events);
        self::assertInstanceOf(ExampleWasCreatedEvent::class, $events->current());
    }

    /**
     * @covers ::markEventAsSaved
     */
    public function testMarkEventAsSavedOnCreatedDummyLetHasUnsavedEventsReturnFalse(): void
    {
        $aggregateRoot = ExampleAggregateRoot::create(
            'example-required-value',
            'example-optional-value',
        );

        foreach ($aggregateRoot->getUnsavedEvents() as $event) {
            assert($event instanceof EventInterface);

            $aggregateRoot->markEventAsSaved($event);
        }

        self::assertFalse($aggregateRoot->hasUnsavedEvents());
    }

    private function simulateSaveAction(AggregateRootInterface $aggregateRoot): void
    {
        foreach ($aggregateRoot->getUnsavedEvents() as $event) {
            assert($event instanceof EventInterface);

            $aggregateRoot->markEventAsSaved($event);
        }
    }
}
