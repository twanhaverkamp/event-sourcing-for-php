<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcing\AggregateRoot\Example;

use DateTimeImmutable;
use TwanHaverkamp\EventSourcing\AggregateRoot\Anonymizable;
use TwanHaverkamp\EventSourcing\Event\Example\ExampleOptionalValueWasChangedEvent;
use TwanHaverkamp\EventSourcing\Event\Example\ExampleRequiredValueWasChangedEvent;
use TwanHaverkamp\EventSourcing\Event\Example\ExampleWasCreatedEvent;
use TwanHaverkamp\EventSourcing\EventStore\Example\ExampleEventStore;
use TwanHaverkamp\EventSourcing\Uuid\Uuid;

/**
 * Note: This class only exists for test purposes.
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class ExampleAggregateRoot extends Anonymizable
{
    public static function getEventStoreClass(): string
    {
        return ExampleEventStore::class;
    }

    public static function create(string $requiredValue, ?string $optionalValue = null): self
    {
        $aggregateRoot = new self($aggregateRootId = Uuid::init());
        $aggregateRoot->recordEvent(new ExampleWasCreatedEvent(
            $requiredValue,
            $optionalValue,
            $aggregateRootId,
        ));

        return $aggregateRoot;
    }

    public function changeRequiredValue(string $requiredValue): void
    {
        $this->recordEvent(new ExampleRequiredValueWasChangedEvent(
            $requiredValue,
            $this->getId(),
        ));
    }

    public function changeOptionalValue(string|null $optionalValue): void
    {
        $this->recordEvent(new ExampleOptionalValueWasChangedEvent(
            $optionalValue,
            $this->getId(),
            new DateTimeImmutable(),
        ));
    }
}
