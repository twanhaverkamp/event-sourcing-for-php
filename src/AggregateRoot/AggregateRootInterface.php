<?php

namespace TwanHaverkamp\EventSourcingForPhp\AggregateRoot;

use TwanHaverkamp\EventSourcingForPhp\Event\EventCollection;
use TwanHaverkamp\EventSourcingForPhp\Event\EventInterface;
use TwanHaverkamp\EventSourcingForPhp\EventStore\EventStoreInterface;
use TwanHaverkamp\EventSourcingForPhp\Uuid\Uuid;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
interface AggregateRootInterface
{
    public static function reconstitute(Uuid $id, EventCollection $events): self;

    /**
     * @return class-string<EventStoreInterface>
     */
    public static function getEventStoreClass(): string;

    public function getId(): Uuid;
    public function getProjection(): object;
    public function hasUnsavedEvents(): bool;
    public function getUnsavedEvents(): EventCollection;
    public function markEventAsSaved(EventInterface $event): void;
}
