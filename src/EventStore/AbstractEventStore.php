<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcing\EventStore;

use Exception;
use TwanHaverkamp\EventSourcing\AggregateRoot\AggregateRootInterface;
use TwanHaverkamp\EventSourcing\Event\EventCollection;
use TwanHaverkamp\EventSourcing\Event\EventInterface;
use TwanHaverkamp\EventSourcing\EventStore\Trait\GetEventClassesTrait;
use TwanHaverkamp\EventSourcing\Exception\EventStoreException;
use TwanHaverkamp\EventSourcing\Uuid\Uuid;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
abstract class AbstractEventStore implements EventStoreInterface
{
    use GetEventClassesTrait;

    abstract protected function loadEvents(Uuid $aggregateRootId): EventCollection;
    abstract protected function saveEvent(EventInterface $event): void;

    final public function getClassName(): string
    {
        return static::class;
    }

    /**
     * @throws EventStoreException if something went wrong while loading data
     *                             for the given {@see AggregateRootInterface}.
     */
    final public function load(Uuid $aggregateRootId, string $aggregateRootClass): AggregateRootInterface
    {
        assert(is_subclass_of($aggregateRootClass, AggregateRootInterface::class));

        try {
            $events = $this->loadEvents($aggregateRootId);
        } catch (Exception $e) {
            throw new EventStoreException(
                "Something went wrong while loading data for aggregate root with ID '{$aggregateRootId->toString()}'.",
                $e
            );
        }

        return $aggregateRootClass::reconstitute($aggregateRootId, $events);
    }

    /**
     * @throws EventStoreException if something went wrong while saving an {@see EventInterface}
     *                             for the given {@see AggregateRootInterface}.
     */
    final public function save(AggregateRootInterface $aggregateRoot): void
    {
        if ($aggregateRoot->hasUnsavedEvents() === false) {
            return;
        }

        foreach ($aggregateRoot->getUnsavedEvents() as $event) {
            if ($event === false) {
                continue;
            }

            try {
                $this->saveEvent($event);
            } catch (Exception $e) {
                throw new EventStoreException(
                    sprintf(
                        'Something went wrong while saving an event of type \'%s\' for aggregate root with ID \'%s\'.',
                        $event::getType(),
                        $aggregateRoot->getId()->toString(),
                    ),
                    $e
                );
            }

            $aggregateRoot->markEventAsSaved($event);
        }
    }
}
