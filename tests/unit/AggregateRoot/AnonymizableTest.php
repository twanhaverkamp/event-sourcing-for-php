<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingForPhp\Tests\Unit\AggregateRoot;

use PHPUnit\Framework\TestCase;
use TwanHaverkamp\EventSourcingForPhp\AggregateRoot\AggregateRootInterface;
use TwanHaverkamp\EventSourcingForPhp\AggregateRoot\Example\ExampleAggregateRoot;
use TwanHaverkamp\EventSourcingForPhp\Event\EventInterface;

/**
 * @coversDefaultClass \TwanHaverkamp\EventSourcingForPhp\AggregateRoot\Anonymizable
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class AnonymizableTest extends TestCase
{
    /**
     * @covers ::anonymize
     */
    public function testAnonymizeWithAggregateRootWithSingleAnonymizableEventResultsInAnonymizedProjection(): void
    {
        $aggregateRoot = ExampleAggregateRoot::create(
            'example-required-value',
            'example-optional-value',
        );
        $aggregateRoot->anonymize();

        $this->simulateSaveAction($aggregateRoot);

        $projection = $aggregateRoot->getProjection();

        self::assertEquals('anonymized-required-value', $projection->requiredValue ?? null);
        self::assertEquals('example-optional-value', $projection->optionalValue ?? null);
    }

    /**
     * @covers ::anonymize
     */
    public function testAnonymizeWithAggregateRootWithMultipleAnonymizableEventsResultsInAnonymizedProjection(): void
    {
        $aggregateRoot = ExampleAggregateRoot::create(
            'init-required-value',
            'init-optional-value',
        );
        $aggregateRoot->changeRequiredValue('changed-required-value');
        $aggregateRoot->changeOptionalValue('changed-optional-value');
        $aggregateRoot->anonymize();

        $this->simulateSaveAction($aggregateRoot);

        $projection = $aggregateRoot->getProjection();

        self::assertEquals('anonymized-required-value', $projection->requiredValue ?? null);
        self::assertEquals('changed-optional-value', $projection->optionalValue ?? null);
    }

    private function simulateSaveAction(AggregateRootInterface $aggregateRoot): void
    {
        foreach ($aggregateRoot->getUnsavedEvents() as $event) {
            assert($event instanceof EventInterface);

            $aggregateRoot->markEventAsSaved($event);
        }
    }
}
