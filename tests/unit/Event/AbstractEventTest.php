<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcing\Tests\Unit\Event;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use TwanHaverkamp\EventSourcing\Event\Example\ExampleWasCreatedEvent;
use TwanHaverkamp\EventSourcing\Uuid\Uuid;

/**
 * @coversDefaultClass \TwanHaverkamp\EventSourcing\Event\AbstractEvent
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class AbstractEventTest extends TestCase
{
    /**
     * @covers ::getType
     */
    public function testGetTypeReturnsExpectedSnakeCasedString(): void
    {
        self::assertEquals('example_was_created_event', ExampleWasCreatedEvent::getType());
    }

    /**
     * @covers ::getAggregateRootId
     */
    public function testGetAggregateRootIdReturnsConstructedUuid(): void
    {
        $aggregateRootId = Uuid::init();
        $event = new ExampleWasCreatedEvent(
            'example-required-value',
            'example-optional-value',
            $aggregateRootId,
        );

        self::assertEquals($aggregateRootId, $event->getAggregateRootId());
    }

    /**
     * @covers ::getRecordedAt
     */
    public function testGetRecordedAtReturnsConstructedDateTimeImmutable(): void
    {
        $recordedAt = new DateTimeImmutable();
        $event = new ExampleWasCreatedEvent(
            'example-required-value',
            'example-optional-value',
            Uuid::init(),
            $recordedAt,
        );

        self::assertEquals($recordedAt, $event->getRecordedAt());
    }
}
