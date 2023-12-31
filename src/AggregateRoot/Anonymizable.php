<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingForPhp\AggregateRoot;

use TwanHaverkamp\EventSourcingForPhp\Event\AnonymizableInterface;
use TwanHaverkamp\EventSourcingForPhp\Event\EventCollection;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
abstract class Anonymizable extends AbstractAggregateRoot
{
    final public function anonymize(): void
    {
        $this->anonymizeEvents($this->events);
        $this->anonymizeEvents($this->unsavedEvents);
    }

    private function anonymizeEvents(EventCollection $events): void
    {
        foreach ($events as $event) {
            if (!$event instanceof AnonymizableInterface) {
                continue;
            }

            $event->anonymize();

            $this->events->remove($event);
            $this->unsavedEvents->add($event);
        }
    }
}
