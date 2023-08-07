<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingForPhp\EventStore;

use DateTimeInterface;
use InvalidArgumentException;
use LogicException;
use TwanHaverkamp\EventSourcingForPhp\Event\EventCollection;
use TwanHaverkamp\EventSourcingForPhp\Event\EventInterface;
use TwanHaverkamp\EventSourcingForPhp\Factory\DateTimeFactory;
use TwanHaverkamp\EventSourcingForPhp\Uuid\Uuid;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class JsonFileEventStore extends AbstractEventStore
{
    /**
     * @var string
     */
    public const
        FILE_EXTENSION   = 'json',
        FILENAME_PATTERN = '/^(\d{16})((_[a-z]+)+)\.' . self::FILE_EXTENSION . '$/i';

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

        foreach ($this->loadFilenames($aggregateRootId, $recordedAt, $type) as $filename) {
            /** @var class-string<EventInterface> $eventClass */
            if (($eventClass = self::getEventClassByType($type)) === null) {
                throw new LogicException("Unable to find event class for type '$type'.");
            }

            if (($content = file_get_contents($filename)) === false) {
                throw new LogicException("Unable to get contents from '$filename'.");
            }

            if (($payload = json_decode($content, true)) === null) {
                throw new LogicException("Unable to decode contents from '$filename'.");
            }

            /** @var non-empty-array<string, string|null> $payload */
            $events->add(
                $eventClass::reconstruct($aggregateRootId, $recordedAt, $payload)
            );
        }

        return $events;
    }

    /**
     * @throws InvalidArgumentException if the constructed directory cannot be created.
     * @throws LogicException           if the event cannot be saved into a file.
     */
    protected function saveEvent(EventInterface $event): void
    {
        $path = $this->getPath($event->getAggregateRootId());
        if (file_exists($path) === false) {
            if (mkdir(directory: $path, recursive: true) === false) {
                throw new InvalidArgumentException("Unable to create directory '$path'.");
            }
        }

        if (file_put_contents(($filename = $this->getFilename($event)), json_encode($event->getPayload())) === false) {
            throw new LogicException("Unable to save '$filename'.");
        }
    }

    private function getFilename(EventInterface $event): string
    {
        return sprintf(
            '%s/%d_%s.' . self::FILE_EXTENSION,
            $this->getPath($event->getAggregateRootId()),
            $event->getRecordedAt()->format(EventInterface::RECORDED_AT_FORMAT),
            $event::getType(),
        );
    }

    private function getPath(Uuid $aggregateRootId): string
    {
        return "$this->dir/{$aggregateRootId->toRfc4122()}";
    }

    /**
     * @return iterable<string>
     */
    private function loadFilenames(
        Uuid $aggregateRootId,
        ?DateTimeInterface &$recordedAt,
        ?string &$type,
    ): iterable {
        foreach (scandir($this->getPath($aggregateRootId), SCANDIR_SORT_ASCENDING) ?: [] as $filename) {
            if (preg_match(self::FILENAME_PATTERN, $filename, $matches) !== 1) {
                continue;
            }

            $recordedAt = DateTimeFactory::createImmutableFromMicroseconds((int)$matches[1]);
            $type = ltrim($matches[2], '_');

            yield sprintf(
                '%s/%s',
                $this->getPath($aggregateRootId),
                $filename,
            );
        }
    }
}
