<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractWidgetDomainEvent extends PlatformDomainEvent
{
    public function __construct(
        private string $id,
        private string $type,
    ) {
    }
}
