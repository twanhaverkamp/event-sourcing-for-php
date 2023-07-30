<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcing\Event\Example;

use DateTimeImmutable;
use TwanHaverkamp\EventSourcing\Event\SerializableEvent;
use TwanHaverkamp\EventSourcing\Uuid\Uuid;

/**
 * Note: This class only exists for test purposes.
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class ExampleOptionalValueWasChangedEvent extends SerializableEvent
{
    public function __construct(
        private string|null $optionalValue,
        Uuid $aggregateRootId,
        DateTimeImmutable $recordedAt = new DateTimeImmutable(),
    ) {
        parent::__construct($aggregateRootId, $recordedAt);
    }

    /**
     * @param non-empty-array<string, string|null> $data
     */
    public function __unserialize(array $data): void
    {
        $this->optionalValue = $data['optionalValue'] ?? null;
        parent::__unserialize($data);
    }

    public function getPayload(): array
    {
        return [
            'optionalValue' => $this->optionalValue,
        ];
    }
}
