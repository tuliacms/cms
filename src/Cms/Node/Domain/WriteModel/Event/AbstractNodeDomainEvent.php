<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractNodeDomainEvent extends PlatformDomainEvent
{
    public function __construct(
        public readonly string $id,
        public readonly string $type
    ) {
    }
}
