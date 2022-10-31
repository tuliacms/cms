<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Assetter;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
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
            RequestEvent::class => ['handle', -999],
        ];
    }

    public function handle(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (
            $this->website->isBackend()
            || strncmp($request->getPathInfo(), '/_wdt', 5) === 0
            || strncmp($request->getPathInfo(), '/_profiler', 10) === 0
        ) {
            return;
        }

        $this->loader->load();
    }
}
