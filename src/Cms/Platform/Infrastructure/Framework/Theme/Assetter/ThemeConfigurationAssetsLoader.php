<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Theme\Assetter;

use phpDocumentor\Reflection\Types\Integer;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Tulia\Component\Routing\Website\WebsiteInterface;
use Tulia\Component\Theme\Assetter\ThemeConfigurationAssetsLoader as BaseLoader;
use Tulia\Component\Theme\Exception\ThemeConfigurationNotResolverException;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeConfigurationAssetsLoader implements EventSubscriberInterface
{
    public function __construct(
        private readonly BaseLoader $loader,
        private readonly WebsiteInterface $website,
        private readonly LoggerInterface $logger,
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
        if ($event instanceof ViewEvent && $event->getRequest()->server->get('TULIA_WEBSITE_IS_BACKEND')) {
            return;
        }

        try {
            $this->loader->load($this->website->getId(), $this->website->getLocale()->getCode());
        } catch (ThemeConfigurationNotResolverException $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
