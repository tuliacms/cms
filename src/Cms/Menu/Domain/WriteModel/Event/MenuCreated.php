<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent as BaseAbstractDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
class MenuCreated extends BaseAbstractDomainEvent
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
