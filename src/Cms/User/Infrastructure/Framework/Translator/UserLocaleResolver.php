<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Translator;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;

/**
 * Listener sets current locale in Request to logged-in user defined.
 * Works only on backend.
 *
 * @author Adam Banaszkiewicz
 */
class UserLocaleResolver implements EventSubscriberInterface
{
    public function __construct(
        private AuthenticatedUserProviderInterface $authenticatedUserProvider,
        private Translator $translator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['handle', 5],
        ];
    }

    public function handle(RequestEvent $event): void
    {
        if (!$event->getRequest()->server->get('TULIA_WEBSITE_IS_BACKEND')) {
            return;
        }

        $user = $this->authenticatedUserProvider->getUser();

        if (!$user->getId()) {
            $locale = $event->getRequest()->server->get('TULIA_WEBSITE_LOCALE');
        } else {
            $locale = $this->authenticatedUserProvider->getUser()->getLocale();
        }

        $this->translator->setLocale($locale);
    }
}
