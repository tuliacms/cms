<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Theme\Assetter;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Tulia\Component\Theme\Assetter\ThemeConfigurationAssetsLoader as BaseLoader;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeConfigurationAssetsLoader implements EventSubscriberInterface
{
    protected BaseLoader $loader;

    public function __construct(BaseLoader $loader)
    {
        $this->loader = $loader;
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
        if ($event instanceof ViewEvent && $event->getRequest()->server->get('TULIA_WEBSITE_IS_BACKEND')) {
            return;
        }

        $this->loader->load();
    }
}
