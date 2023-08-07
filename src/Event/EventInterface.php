<?php

namespace TwanHaverkamp\EventSourcingForPhp\Event;

use DateTimeInterface;
use TwanHaverkamp\EventSourcingForPhp\Uuid\Uuid;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
interface EventInterface
{
    /**
     * @var string
     */
    public const RECORDED_AT_FORMAT = 'Uu';

    /**
     * @param array<string, mixed> $payload
     */
    public static function reconstruct(Uuid $aggregateRootId, DateTimeInterface $recordedAt, array $payload): self;
    public static function getType(): string;

    public function getAggregateRootId(): Uuid;
    public function getRecordedAt(): DateTimeInterface;

    /**
     * @return array<string, mixed>
     */
    public function getPayload(): array;
}
