<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\EventListener\ExceptionListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Exception\NotDevelopmentEnvironmentAccessDeniedException;

/**
 * @author Adam Banaszkiewicz
 */
final class NotDevelopmentEnvironmentExceptionListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [ExceptionEvent::class => 'handle'];
    }

    public function handle(ExceptionEvent $event): void
    {
        if ($event->getThrowable() instanceof NotDevelopmentEnvironmentAccessDeniedException) {
            $request = $this->requestStack->getMainRequest();

            if ($request instanceof Request) {
                $request->getSession()->getFlashBag()->add(
                    'danger', $this->translator->trans('cannotDoThisOperationOnNotDevEnvironment')
                );

                $referer = null;

                if ($request->headers->has('referer')) {
                    $maybeReferer = $request->headers->get('referer');

                    if ($request->getHost() === parse_url($maybeReferer, PHP_URL_HOST)) {
                        $referer = $maybeReferer;
                    }
                }

                if (!$referer) {
                    $referer = $this->urlGenerator->generate('backend.homepage');
                }

                $event->setResponse(new RedirectResponse($referer));
                return;
            }
        }
    }
}
