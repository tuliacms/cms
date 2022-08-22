<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Security\Core\Security;
use Tulia\Component\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class WebsiteActiveness implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly string $environment,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 7],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($this->isSymfonyInternalPage($event->getRequest())) {
            return;
        }

        $website = $event->getRequest()->attributes->get('website');

        if (!$website instanceof WebsiteInterface) {
            throw new RouteNotFoundException('Page not found.');
        }

        if (!$website->isActive() && !$website->isBackend() && !$this->security->isGranted('ROLE_ADMIN')) {
            throw new RouteNotFoundException(
                $this->environment === 'dev'
                    ? 'Website exists, but this is not a backend request and You are not ROLE_ADMIN user.'
                    : 'Page not found.'
            );
        }
    }

    private function isSymfonyInternalPage(Request $request): bool
    {
        return false !== strpos($request->getPathInfo(), '/_wdt')
            || false !== strpos($request->getPathInfo(), '/_profiler')
            || false !== strpos($request->getPathInfo(), '/_fragment')
            || $request->attributes->get('error_controller') === 'error_controller'
            || $request->attributes->has('exception');
    }
}
