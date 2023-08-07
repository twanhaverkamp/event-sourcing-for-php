<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingForPhp\Event;

use DateTimeImmutable;
use Serializable;
use TwanHaverkamp\EventSourcingForPhp\Uuid\Uuid;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
abstract class SerializableEvent extends AbstractEvent implements Serializable
{
    /**
     * @var string
     */
    final protected const RECORDED_AT_FORMAT = 'Y-m-d H:i:s.u';

    final public function serialize(): string
    {
        return json_encode($this->__serialize()) ?: '';
    }

    final public function unserialize(string $data): void
    {
        /** @var non-empty-array<string, mixed> $serialized */
        $serialized = json_decode($data, true);

        if (is_array($serialized) === true) {
            $this->__unserialize($serialized);
        }
    }

    final public function __serialize(): array
    {
        return array_merge([
            'aggregateRootId' => $this->getAggregateRootId()->toRfc4122(),
            'recordedAt' => $this->getRecordedAt()->format(self::RECORDED_AT_FORMAT),
        ], $this->getPayload());
    }

    /**
     * @param non-empty-array<string, mixed> $data
     */
    public function __unserialize(array $data): void
    {
        assert(is_string($data['recordedAt'] ?? null) === true);
        $recordedAt = DateTimeImmutable::createFromFormat(
            self::RECORDED_AT_FORMAT,
            $data['recordedAt'],
        );

        assert($recordedAt instanceof DateTimeImmutable);
        assert(is_string($data['aggregateRootId'] ?? null) === true);

        parent::__construct(
            Uuid::fromRfc4122($data['aggregateRootId']),
            $recordedAt,
        );
    }
}
