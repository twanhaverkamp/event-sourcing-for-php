<?php

namespace TwanHaverkamp\EventSourcing\Event;

use TwanHaverkamp\EventSourcing\AggregateRoot\AggregateRootInterface;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
interface AnonymizableInterface
{
    /**
     * To protect privacy-sensitive data, you can anonymize it with this method.
     *
     * Warning: Once you have done this and the {@see AggregateRootInterface} has been saved,
     * you will not be able to recover the original data!
     *
     * Note: Unique values must still be unique after anonymization.
     */
    public function anonymize(): void;
}
