<?php

namespace TwanHaverkamp\EventSourcingForPhp\Event;

use DateTimeInterface;
use TwanHaverkamp\EventSourcingForPhp\Uuid\Uuid;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
interface EventInterface
{
    public static function getType(): string;

    public function getAggregateRootId(): Uuid;
    public function getRecordedAt(): DateTimeInterface;

    /**
     * @return array<string, mixed>
     */
    public function getPayload(): array;
}
