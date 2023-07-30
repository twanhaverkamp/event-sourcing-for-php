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
final class ExampleRequiredValueWasChangedEvent extends SerializableEvent implements AnonymizableInterface
{
    public function __construct(
        private string $requiredValue,
        Uuid $aggregateRootId,
        DateTimeImmutable $recordedAt = new DateTimeImmutable(),
    ) {
        parent::__construct($aggregateRootId, $recordedAt);
    }

    /**
     * @param non-empty-array<string, string> $data
     */
    public function __unserialize(array $data): void
    {
        $this->requiredValue = (string)($data['requiredValue'] ?? null);

        parent::__unserialize($data);
    }

    public function getPayload(): array
    {
        return [
            'requiredValue' => $this->requiredValue,
        ];
    }

    public function anonymize(): void
    {
        $this->requiredValue = 'anonymized-required-value';
    }
}
