<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcing\EventStore;

use TwanHaverkamp\EventSourcing\AggregateRoot\AggregateRootInterface;
use TwanHaverkamp\EventSourcing\Exception\EventStoreNotFoundException;
use TwanHaverkamp\EventSourcing\Uuid\Uuid;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class EventStoreManager
{
    public function __construct(
        /**
         * @var iterable<EventStoreInterface>
         */
        private iterable $stores
    ) {
    }

    /**
     * @param class-string<AggregateRootInterface> $aggregateRootClass
     */
    public function load(Uuid $aggregateRootId, string $aggregateRootClass): AggregateRootInterface
    {
        return $this
            ->getEventStore($aggregateRootClass::getEventStoreClass())
            ->load($aggregateRootId, $aggregateRootClass);
    }

    public function save(AggregateRootInterface $aggregateRoot): void
    {
        $this
            ->getEventStore($aggregateRoot::getEventStoreClass())
            ->save($aggregateRoot);
    }

    /**
     * @param class-string<EventStoreInterface> $eventStoreClass
     *
     * @throws EventStoreNotFoundException if {@see AggregateRootInterface::getEventStoreClass()} cannot be found.
     */
    private function getEventStore(string $eventStoreClass): EventStoreInterface
    {
        assert(is_subclass_of($eventStoreClass, EventStoreInterface::class) === true);

        if (class_exists($eventStoreClass) === false) {
            throw new EventStoreNotFoundException("Class '$eventStoreClass' is not defined.");
        }

        foreach ($this->stores as $store) {
            assert($store instanceof EventStoreInterface);

            if ($eventStoreClass === $store->getClassName()) {
                return $store;
            }
        }

        throw new EventStoreNotFoundException(sprintf(
            'Unable to find an event store with class \'%s\'. Did you forget to implement \'%s\'.',
            $eventStoreClass,
            EventStoreInterface::class,
        ));
    }
}
