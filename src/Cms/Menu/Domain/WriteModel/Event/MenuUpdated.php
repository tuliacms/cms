<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
class MenuUpdated extends AbstractDomainEvent
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
