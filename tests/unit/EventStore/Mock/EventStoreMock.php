<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingForPhp\Tests\Unit\EventStore\Mock;

use TwanHaverkamp\EventSourcingForPhp\AggregateRoot\AggregateRootInterface;
use TwanHaverkamp\EventSourcingForPhp\EventStore\EventStoreInterface;
use TwanHaverkamp\EventSourcingForPhp\Uuid\Uuid;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
class EventStoreMock implements EventStoreInterface
{
    public function __construct(
        private readonly AggregateRootInterface $aggregateRoot
    ) {
    }

    public function getClassName(): string
    {
        return self::class;
    }

    public function load(Uuid $aggregateRootId, string $aggregateRootClass): AggregateRootInterface
    {
        return $this->aggregateRoot;
    }

    public function save(AggregateRootInterface $aggregateRoot): void
    {
    }
}
