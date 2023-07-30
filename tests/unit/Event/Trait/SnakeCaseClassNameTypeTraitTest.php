<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcing\Tests\Unit\Event\Trait;

use PHPUnit\Framework\TestCase;
use TwanHaverkamp\EventSourcing\Event\Trait\SnakeCaseClassNameTypeTrait;

/**
 * @coversDefaultClass \TwanHaverkamp\EventSourcing\Event\Trait\SnakeCaseClassNameTypeTrait
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
