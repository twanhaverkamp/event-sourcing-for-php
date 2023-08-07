<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingForPhp\Tests\Unit\Factory;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TwanHaverkamp\EventSourcingForPhp\Factory\DateTimeFactory;

/**
 * @coversDefaultClass \TwanHaverkamp\EventSourcingForPhp\Factory\DateTimeFactory
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class DateTimeFactoryTest extends TestCase
{
    /**
     * @covers ::createImmutableFromMicroseconds
     */
    public function testCreateImmutableFromMicrosecondsReturnsDateTimeImmutable(): void
    {
        $dt = DateTimeFactory::createImmutableFromMicroseconds(1691429400123456);

        $this->assertEquals('1691429400123456', $dt->format('Uu'));
    }

    /**
     * @dataProvider getInvalidArguments
     *
     * @covers ::createImmutableFromMicroseconds
     */
    public function testCreateImmutableFromMicrosecondsWithInvalidLengthThrowsInvalidArgumentException(
        int $microseconds,
    ): void {
        $this->expectException(InvalidArgumentException::class);

        DateTimeFactory::createImmutableFromMicroseconds($microseconds);
    }

    /**
     * @return non-empty-array<string, non-empty-array<int>>
     */
    public static function getInvalidArguments(): array
    {
        return [
            'Argument with 15 digits' => [169142940012345],
            'Argument with 17 digits' => [16914294001234567],
        ];
    }
}
