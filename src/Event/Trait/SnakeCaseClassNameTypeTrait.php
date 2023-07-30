<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcing\Event\Trait;

use ReflectionClass;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
trait SnakeCaseClassNameTypeTrait
{
    /**
     * Returns its class name as a snake_case string.
     *
     * @example \Foo\Bar\BazQuuxQuuuxEvent results in "baz_quux_quuux_event".
     */
    public static function getType(): string
    {
        $className = (new ReflectionClass(static::class))
            ->getShortName();

        return strtolower(
            preg_replace('/[A-Z]/', '_$0', lcfirst($className)) ?: ''
        );
    }
}
