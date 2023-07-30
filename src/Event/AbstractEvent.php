<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcing\Event;

use DateTimeInterface;
use TwanHaverkamp\EventSourcing\Event\Trait\SnakeCaseClassNameTypeTrait;
use TwanHaverkamp\EventSourcing\Uuid\Uuid;

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
