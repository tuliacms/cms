<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
final class WebsiteActivityChanged extends AbstractDomainEvent
{
    public function __construct(
        public readonly string $id,
        public readonly bool $active,
    ) {
    }
}
