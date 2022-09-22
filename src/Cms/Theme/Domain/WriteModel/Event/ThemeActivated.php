<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Domain\WriteModel\Event;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeActivated extends DomainEvent
{
    public function __construct(
        private readonly string $themeName
    ) {
    }
}
