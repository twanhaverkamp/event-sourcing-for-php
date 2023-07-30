<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcing\EventStore\Example;

use InvalidArgumentException;
use LogicException;
use TwanHaverkamp\EventSourcing\Event\EventCollection;
use TwanHaverkamp\EventSourcing\Event\EventInterface;
use TwanHaverkamp\EventSourcing\EventStore\AbstractEventStore;
use TwanHaverkamp\EventSourcing\Uuid\Uuid;

/**
 * Note: This class only exists for test purposes and stores serialized events in the constructed directory.
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class ExampleEventStore extends AbstractEventStore
{
    public function __construct(
        private readonly string $dir
    ) {
    }

    /**
     * @throws LogicException if the event file cannot be opened or
     *                        if the content of the file cannot be decoded.
     */
    protected function loadEvents(Uuid $aggregateRootId): EventCollection
    {
        $events = new EventCollection();

        foreach (glob("{$this->getPath($aggregateRootId)}/*.tmp") ?: [] as $filename) {
            if (($serialized = file_get_contents($filename)) === false) {
                throw new LogicException("Unable to get contents from '$filename'.");
            }

            $event = unserialize($serialized, [
                'allowed_classes' => self::getEventClasses(),
            ]);

            if ($event instanceof EventInterface) {
                $events->add($event);
            }
        }

        return $events;
    }

    /**
     * Note: This class requires that your events implement {@see TransfableEventInterface}.
     *
     * @throws InvalidArgumentException if the constructed directory cannot be created.
     * @throws LogicException           if the serialized event cannot be saved into a file.
     */
    protected function saveEvent(EventInterface $event): void
    {
        $path = $this->getPath($event->getAggregateRootId());
        if (file_exists($path) === false) {
            if (mkdir(directory: $path, recursive: true) === false) {
                throw new InvalidArgumentException("Unable to create directory '$path'.");
            }
        }

        $serialized = serialize($event);
        if (file_put_contents(($filename = $this->getFilename($event)), $serialized) === false) {
            throw new LogicException("Unable to save '$filename'.");
        }
    }

    private function getFilename(EventInterface $event): string
    {
        return sprintf(
            '%s/%d_%s.tmp',
            $this->getPath($event->getAggregateRootId()),
            $event->getRecordedAt()->format('Uu'),
            $event::getType(),
        );
    }

    private function getPath(Uuid $aggregateRootId): string
    {
        return "$this->dir/{$aggregateRootId->toString()}";
    }
}
