<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingForPhp\Exception;

use Exception;

/**
 * Note: Use this type to wrap all exceptions thrown at the event store level.
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class EventStoreException extends Exception
{
    public function __construct(string $message, Exception $e)
    {
        parent::__construct(message: $message, previous: $e);
    }
}
