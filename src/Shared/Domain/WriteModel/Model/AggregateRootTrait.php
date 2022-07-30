<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel\Model;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
trait AggregateRootTrait
{
    /** @var AbstractDomainEvent[] */
    protected array $domainEvents = [];

    public function collectDomainEvents(): array
    {
        $events = $this->domainEvents;

        $this->domainEvents = [];

        return $events;
    }

    protected function recordThat(AbstractDomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    protected function recordUniqueThat(AbstractDomainEvent $event, callable $isDuplicatedEvent): void
    {
        foreach ($this->domainEvents as $key => $item) {
            if (is_a($item, $event::class) && $isDuplicatedEvent($item)) {
                unset($this->domainEvents[$key]);
            }
        }

        $this->domainEvents[] = $event;
    }
}
