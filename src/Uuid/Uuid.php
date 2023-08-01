<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingForPhp\Uuid;

use Symfony\Component\Uid\Uuid as BaseUuid;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class Uuid implements UuidInterface
{
    public static function init(): self
    {
        return new self(
            BaseUuid::v4(),
        );
    }

    private function __construct(
        private BaseUuid $uuid,
    ) {
    }

    public static function isValid(string $uuid): bool
    {
        return BaseUuid::isValid($uuid);
    }

    public static function fromString(string $uuid): static
    {
        assert(BaseUuid::isValid($uuid));

        return new self(
            BaseUuid::fromString($uuid),
        );
    }

    public function toString(): string
    {
        return $this->uuid->toRfc4122();
    }
}
