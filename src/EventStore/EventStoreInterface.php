<?php

namespace TwanHaverkamp\EventSourcingForPhp\EventStore;

use TwanHaverkamp\EventSourcingForPhp\AggregateRoot\AggregateRootInterface;
use TwanHaverkamp\EventSourcingForPhp\Uuid\Uuid;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
interface EventStoreInterface
{
    /**
     * This method was introduced to be able to test with mocked {@see EventStoreInterface} instances.
     *
     * @return class-string<EventStoreInterface>
     */
    public function getClassName(): string;

    /**
     * @param class-string<AggregateRootInterface> $aggregateRootClass
     */
    public function load(Uuid $aggregateRootId, string $aggregateRootClass): AggregateRootInterface;

    public function save(AggregateRootInterface $aggregateRoot): void;
}
