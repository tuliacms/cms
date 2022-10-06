<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Infrastructure\Framework\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Tulia\Cms\Settings\Domain\Event\SettingsUpdated;

/**
 * @author Adam Banaszkiewicz
 */
final class ClearCacheWhenSettingsUpdated implements EventSubscriberInterface
{
    public function __construct(
        private readonly TagAwareCacheInterface $settingsCache,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SettingsUpdated::class => 'clearCache',
        ];
    }

    public function clearCache(): void
    {
        $this->settingsCache->invalidateTags(['settings']);
    }
}
