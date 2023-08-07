<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingForPhp\Factory;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class DateTimeFactory
{
    /**
     * By default, it's not possible to create a {@see DateTimeInterface} instance based on microseconds.
     *
     * @throws InvalidArgumentException if $microsecond doesn't consist 16 digits or
     *                                  if {@see DateTimeImmutable::createFromFormat} returns false.
     */
    public static function createImmutableFromMicroseconds(int $microseconds): DateTimeInterface
    {
        if (($length = strlen((string)$microseconds)) !== 16) {
            throw new InvalidArgumentException(sprintf(
                'Invalid microseconds value given. Expected 16 digits, got %d.',
                $length,
            ));
        }

        $format = sprintf(
            '%d.%d',
            substr((string)$microseconds, 0, 10),
            substr((string)$microseconds, 10, 6),
        );

        if (($dt = DateTimeImmutable::createFromFormat('U.u', $format)) === false) {
            throw new InvalidArgumentException(sprintf(
                'Unable to create a \'%s\' instance for \'%d\'.',
                DateTimeImmutable::class,
                $microseconds,
            ));
        }

        return $dt;
    }
}
