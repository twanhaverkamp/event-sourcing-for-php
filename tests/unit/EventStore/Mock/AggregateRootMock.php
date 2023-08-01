<?php

namespace TwanHaverkamp\EventSourcingForPhp\Tests\Unit\EventStore\Mock;

use TwanHaverkamp\EventSourcingForPhp\AggregateRoot\AbstractAggregateRoot;
use TwanHaverkamp\EventSourcingForPhp\Uuid\Uuid;

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
