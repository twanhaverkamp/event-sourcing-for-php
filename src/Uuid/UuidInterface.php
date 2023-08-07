<?php

namespace TwanHaverkamp\EventSourcingForPhp\Uuid;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
interface UuidInterface
{
    public static function init(): self;
    public static function isValid(string $uuid): bool;
    public static function fromRfc4122(string $uuid): self;

    public function toRfc4122(): string;
}
