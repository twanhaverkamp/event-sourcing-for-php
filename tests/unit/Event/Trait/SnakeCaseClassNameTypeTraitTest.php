<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingForPhp\Tests\Unit\Event\Trait;

use PHPUnit\Framework\TestCase;
use TwanHaverkamp\EventSourcingForPhp\Event\Trait\SnakeCaseClassNameTypeTrait;

/**
 * @coversDefaultClass \TwanHaverkamp\EventSourcingForPhp\Event\Trait\SnakeCaseClassNameTypeTrait
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class SnakeCaseClassNameTypeTraitTest extends TestCase
{
    use SnakeCaseClassNameTypeTrait;

    /**
     * @covers ::getType
     */
    public function testGetTypeReturnsClassNameAsSnakeCaseWithoutNamespace(): void
    {
        self::assertSame('snake_case_class_name_type_trait_test', self::getType());
    }
}
