<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
final class LocaleActivityChanged extends AbstractDomainEvent
{
    public function __construct(
        public readonly string $websiteId,
        public readonly string $code,
        public readonly bool $active,
    ) {
    }
}
