<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;

/**
 * @author Adam Banaszkiewicz
 */
final class AggregateRootSpy
{
    /** @var AbstractDomainEvent[][] */
    private array $aggregateEventsCollection = [];
    private ?AbstractAggregateRoot $aggregate;

    public function __construct(?AbstractAggregateRoot $aggregate)
    {
        $this->aggregate = $aggregate;
    }

    /**
     * @return AbstractDomainEvent[]
     */
    public function collectEvents(): array
    {
        if ($this->aggregate === null) {
            return [];
        }

        if (isset($this->aggregateEventsCollection[spl_object_id($this->aggregate)])) {
            return $this->aggregateEventsCollection[spl_object_id($this->aggregate)];
        }

        return $this->aggregateEventsCollection[spl_object_id($this->aggregate)]
            = $this->aggregate->collectDomainEvents();
    }

    public function findEvent(string $classname): ?AbstractDomainEvent
    {
        foreach ($this->collectEvents() as $event) {
            if ($event instanceof $classname) {
                return $event;
            }
        }

        return null;
    }

    public function findEventOfAny(array $classNames): ?AbstractDomainEvent
    {
        foreach ($this->collectEvents() as $event) {
            if (\in_array(\get_class($event), $classNames, true)) {
                return $event;
            }
        }

        return null;
    }
}
