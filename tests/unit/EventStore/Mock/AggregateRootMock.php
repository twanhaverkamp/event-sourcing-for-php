<?php

namespace TwanHaverkamp\EventSourcing\Tests\Unit\EventStore\Mock;

use TwanHaverkamp\EventSourcing\AggregateRoot\AbstractAggregateRoot;
use TwanHaverkamp\EventSourcing\Uuid\Uuid;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class AggregateRootMock extends AbstractAggregateRoot
{
    public static function getEventStoreClass(): string
    {
        return EventStoreMock::class;
    }

    public static function create(): self
    {
        return new self(Uuid::init());
    }
}
