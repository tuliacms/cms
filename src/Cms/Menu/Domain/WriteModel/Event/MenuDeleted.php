<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Event;

/**
 * @author Adam Banaszkiewicz
 */
class MenuDeleted extends \Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
