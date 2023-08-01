<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingForPhp\Event;

use Countable;
use Iterator;

/**
 * Note: Events are always sorted ascending by their {@see EventInterface::getRecordedAt()} value.
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class EventCollection implements Countable, Iterator
{
    /**
     * @var array<EventInterface>
     */
    private array $events = [];

    private int $key = 0;

    public function __clone()
    {
        foreach ($this->events as $key => $event) {
            $this->events[$key] = clone $event;
        }
    }

    public function __destruct()
    {
        foreach ($this->events as $event) {
            unset($event);
        }
    }

    public function has(EventInterface $event): bool
    {
        return in_array($event, $this->events, true);
    }

    public function add(EventInterface $event): self
    {
        if ($this->has($event) === false) {
            $this->events[] = $event;
            $this->sort();
        }

        return $this;
    }

    public function remove(EventInterface $event): self
    {
        if (($key = array_search($event, $this->events, true)) !== false) {
            unset($this->events[$key]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->events);
    }

    /**
     * @inheritDoc
     */
    public function current(): EventInterface|false
    {
        return $this->events[$this->key] ?? false;
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        $this->key++;
    }

    /**
     * @inheritDoc
     */
    public function key(): int
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return array_key_exists($this->key, $this->events);
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->key = 0;
    }

    /**
     * Sorts events based on when they were recorded (oldest to newest).
     */
    private function sort(): void
    {
        usort($this->events, function (EventInterface $a, EventInterface $b) {
            $aRegisteredAt = (int)$a->getRecordedAt()->format('Uu');
            $bRegisteredAt = (int)$b->getRecordedAt()->format('Uu');

            if ($aRegisteredAt === $bRegisteredAt) {
                return 0;
            }

            return ($aRegisteredAt < $bRegisteredAt) ? -1 : 1;
        });

        $this->events = array_values($this->events);
        $this->rewind();
    }
}
