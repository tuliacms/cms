<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Translator;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * Listener sets current locale in Request to logged-in user defined.
 * Works only on backend.
 *
 * @author Adam Banaszkiewicz
 */
class UserLocaleResolver implements EventSubscriberInterface
{
    public function __construct(
        private readonly AuthenticatedUserProviderInterface $authenticatedUserProvider,
        private readonly Translator $translator,
        private readonly WebsiteInterface $website,
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
        if (!$this->website->isBackend()) {
            return;
        }

        $user = $this->authenticatedUserProvider->getUser();

        if (!$user->getId()) {
            $locale = $this->website->getLocale()->getCode();
        } else {
            $locale = $this->authenticatedUserProvider->getUser()->getLocale();
        }

        $this->translator->setLocale($locale);
    }
}
