<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Domain\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
final class SettingsUpdated extends AbstractDomainEvent
{
    public function __construct(
        public readonly array $settings,
    ) {
    }

    public function hasBeenChanged(string $name): bool
    {
        return in_array($name, $this->settings, true);
    }
}
