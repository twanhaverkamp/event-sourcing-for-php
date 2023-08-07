<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingForPhp\Event\Example;

use DateTimeImmutable;
use DateTimeInterface;
use TwanHaverkamp\EventSourcingForPhp\Event\AbstractEvent;
use TwanHaverkamp\EventSourcingForPhp\Uuid\Uuid;

/**
 * Note: This class only exists for test purposes.
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class ExampleOptionalValueWasChangedEvent extends AbstractEvent
{
    public function __construct(
        private readonly string|null $optionalValue,
        Uuid $aggregateRootId,
        DateTimeInterface $recordedAt = new DateTimeImmutable(),
    ) {
        parent::__construct($aggregateRootId, $recordedAt);
    }

    /**
     * @param array<string, string|null> $payload
     */
    public static function reconstruct(Uuid $aggregateRootId, DateTimeInterface $recordedAt, array $payload): self
    {
        return new self(
            $payload['optionalValue'] ?? null,
            $aggregateRootId,
            $recordedAt,
        );
    }

    public function getPayload(): array
    {
        return [
            'optionalValue' => $this->optionalValue,
        ];
    }
}
