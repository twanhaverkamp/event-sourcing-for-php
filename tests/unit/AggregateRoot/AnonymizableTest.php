<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcing\Tests\Unit\AggregateRoot;

use PHPUnit\Framework\TestCase;
use TwanHaverkamp\EventSourcing\AggregateRoot\AggregateRootInterface;
use TwanHaverkamp\EventSourcing\AggregateRoot\Example\ExampleAggregateRoot;
use TwanHaverkamp\EventSourcing\Event\EventInterface;

/**
 * @coversDefaultClass \TwanHaverkamp\EventSourcing\AggregateRoot\Anonymizable
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
