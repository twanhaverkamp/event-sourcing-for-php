<?php

namespace TwanHaverkamp\EventSourcing\AggregateRoot;

use TwanHaverkamp\EventSourcing\Event\EventCollection;
use TwanHaverkamp\EventSourcing\Event\EventInterface;
use TwanHaverkamp\EventSourcing\EventStore\EventStoreInterface;
use TwanHaverkamp\EventSourcing\Uuid\Uuid;

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
