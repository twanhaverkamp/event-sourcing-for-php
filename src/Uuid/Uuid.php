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

    public static function fromRfc4122(string $uuid): static
    {
        assert(BaseUuid::isValid($uuid));

        return new self(
            BaseUuid::fromRfc4122($uuid),
        );
    }

    public function toRfc4122(): string
    {
        return $this->uuid->toRfc4122();
    }
}
