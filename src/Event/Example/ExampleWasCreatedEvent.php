<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcing\Event\Example;

use DateTimeImmutable;
use TwanHaverkamp\EventSourcing\Event\AnonymizableInterface;
use TwanHaverkamp\EventSourcing\Event\SerializableEvent;
use TwanHaverkamp\EventSourcing\Uuid\Uuid;

/**
 * Note: This class only exists for test purposes.
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class ExampleWasCreatedEvent extends SerializableEvent implements AnonymizableInterface
{
    public function __construct(
        private string $requiredValue,
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
        $this->requiredValue = (string)($data['requiredValue'] ?? null);
        $this->optionalValue = $data['optionalValue'] ?? null;

        parent::__unserialize($data);
    }

    public function getPayload(): array
    {
        return [
            'requiredValue' => $this->requiredValue,
            'optionalValue' => $this->optionalValue,
        ];
    }

    public function anonymize(): void
    {
        $this->requiredValue = 'anonymized-required-value';
    }
}
