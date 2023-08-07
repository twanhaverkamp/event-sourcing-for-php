<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingForPhp\Event\Example;

use DateTimeImmutable;
use DateTimeInterface;
use TwanHaverkamp\EventSourcingForPhp\Event\AbstractEvent;
use TwanHaverkamp\EventSourcingForPhp\Event\AnonymizableInterface;
use TwanHaverkamp\EventSourcingForPhp\Uuid\Uuid;

/**
 * Note: This class only exists for test purposes.
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class ExampleRequiredValueWasChangedEvent extends AbstractEvent implements AnonymizableInterface
{
    public function __construct(
        private string $requiredValue,
        Uuid $aggregateRootId,
        DateTimeInterface $recordedAt = new DateTimeImmutable(),
    ) {
        parent::__construct($aggregateRootId, $recordedAt);
    }

    /**
     * @param non-empty-array<string, string> $payload
     */
    public static function reconstruct(Uuid $aggregateRootId, DateTimeInterface $recordedAt, array $payload): self
    {
        return new self(
            (string)($payload['requiredValue'] ?? null),
            $aggregateRootId,
            $recordedAt,
        );
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
