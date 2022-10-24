<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;
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
        if (!$this->website->isActive() || !$this->website->getLocale()->isActive()) {
            if (!$this->security->isGranted('ROLE_ADMIN')) {
                $this->render->render();
            }
        }
    }
}
