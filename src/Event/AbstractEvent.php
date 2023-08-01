<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingForPhp\Event;

use DateTimeInterface;
use TwanHaverkamp\EventSourcingForPhp\Event\Trait\SnakeCaseClassNameTypeTrait;
use TwanHaverkamp\EventSourcingForPhp\Uuid\Uuid;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
abstract class AbstractEvent implements EventInterface
{
    use SnakeCaseClassNameTypeTrait;

    public function __construct(
        private readonly Uuid $aggregateRootId,
        private readonly DateTimeInterface $recordedAt,
    ) {
    }

    final public function getAggregateRootId(): Uuid
    {
        return $this->aggregateRootId;
    }

    final public function getRecordedAt(): DateTimeInterface
    {
        return $this->recordedAt;
    }
}
