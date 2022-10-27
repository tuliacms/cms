<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\FirewallMapInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\NotFoundViewRenderer;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class Throw404WhenWebsiteOrLocaleIsInactive implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly WebsiteInterface $website,
        private readonly NotFoundViewRenderer $render,
        private readonly FirewallMapInterface $firewallMap,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'handle',
        ];
    }

    public function handle(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$this->website->isEnabled() || !$this->website->getLocale()->isActive()) {
            $firewallConfig = $this->firewallMap->getFirewallConfig($request);

            // Skip DEV firewals, like Symfony Toolbar
            if ($firewallConfig && $firewallConfig->getName() === 'dev') {
                return;
            }

            if (!$this->security->isGranted('ROLE_ADMIN')) {
                // Skip fragments
                if (strncmp($request->getPathInfo(), '/_fragment', 10) === 0) {
                    return;
                }

                $this->render->render();
            }
        }
    }
}
