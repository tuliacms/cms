<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Assetter;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Tulia\Component\Routing\Website\WebsiteInterface;
use Tulia\Component\Theme\Assetter\ThemeConfigurationAssetsLoader as BaseLoader;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeConfigurationAssetsLoader implements EventSubscriberInterface
{
    public function __construct(
        private readonly BaseLoader $loader,
        private readonly WebsiteInterface $website,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ViewEvent::class => ['handle', 2000],
            KernelEvents::EXCEPTION => ['handle', 2000],
        ];
    }

    public function handle($event): void
    {
        if ($event instanceof ViewEvent && $this->website->isBackend()) {
            return;
        }

        $this->loader->load();
    }
}
