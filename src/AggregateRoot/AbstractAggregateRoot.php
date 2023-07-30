<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcing\AggregateRoot;

use TwanHaverkamp\EventSourcing\Event\EventCollection;
use TwanHaverkamp\EventSourcing\Event\EventInterface;
use TwanHaverkamp\EventSourcing\Uuid\Uuid;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
abstract class AbstractAggregateRoot implements AggregateRootInterface
{
    protected readonly EventCollection $unsavedEvents;

    final protected function __construct(
        protected readonly Uuid $id,
        protected readonly EventCollection $events = new EventCollection(),
    ) {
        $this->unsavedEvents = new EventCollection();
    }

    final public static function reconstitute(Uuid $id, EventCollection $events): AggregateRootInterface
    {
        return new static($id, $events);
    }

    final public function getId(): Uuid
    {
        return $this->id;
    }

    public function getProjection(): object
    {
        return (object)$this->getPayload();
    }

    final public function hasUnsavedEvents(): bool
    {
        return $this->unsavedEvents->count() > 0;
    }

    final public function getUnsavedEvents(): EventCollection
    {
        return $this->unsavedEvents;
    }

    final public function markEventAsSaved(EventInterface $event): void
    {
        $this->events->add($event);
        $this->unsavedEvents->remove($event);
    }

    final protected function recordEvent(EventInterface $event): void
    {
        $this->unsavedEvents->add($event);
    }

    /**
     * Note: Only saved events are included in the payload.
     *
     * @return array<string, mixed>
     */
    final protected function getPayload(): array
    {
        foreach ($this->events as $event) {
            assert($event instanceof EventInterface);

            $payload = array_merge($payload ?? [], $event->getPayload());
        }

        return $payload ?? [];
    }
}
