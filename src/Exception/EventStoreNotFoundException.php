<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcing\Exception;

use LogicException;

final class EventStoreNotFoundException extends LogicException
{
}
