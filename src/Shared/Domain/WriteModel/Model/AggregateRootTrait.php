<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel\Model;

use Tulia\Cms\Shared\Domain\WriteModel\Event\DomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
trait AggregateRootTrait
{
    /** @var DomainEvent[] */
    protected array $domainEvents = [];

    public function collectDomainEvents(): array
    {
        $events = $this->domainEvents;

        $this->domainEvents = [];

        return $events;
    }

    protected function recordThat(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    protected function recordUniqueThat(DomainEvent $event, callable $isDuplicatedEvent): void
    {
        foreach ($this->domainEvents as $key => $item) {
            if ($isDuplicatedEvent($item)) {
                unset($this->domainEvents[$key]);
            }
        }

        $this->domainEvents[] = $event;
    }
}
