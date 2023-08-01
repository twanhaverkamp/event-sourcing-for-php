<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingForPhp\EventStore\Trait;

use TwanHaverkamp\EventSourcingForPhp\Event\EventInterface;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
trait GetEventClassesTrait
{
    /**
     * @var array<class-string<EventInterface>>|null
     */
    private static array|null $eventClasses = null;

    /**
     * @return array<class-string<EventInterface>>
     */
    final protected static function getEventClasses(): array
    {
        self::preloadEventClasses();

        return self::$eventClasses ?? [];
    }

    private static function preloadEventClasses(): void
    {
        if (self::$eventClasses !== null) {
            return;
        }

        self::$eventClasses = array_filter(get_declared_classes(), function (string $class) {
            return is_subclass_of($class, EventInterface::class);
        });
    }
}
