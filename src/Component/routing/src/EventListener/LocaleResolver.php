<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RequestContextAwareInterface;

/**
 * @author Adam Banaszkiewicz
 */
class LocaleResolver implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestContextAwareInterface $router
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => [
                ['handle', 9500],
                // Must be called after \Symfony\Component\HttpKernel\EventListener\LocaleListener::setDefaultLocale
                ['setDefaultLocale', 99],
            ],
        ];
    }

    public function handle(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $parameters = $request->attributes->all();
        $parameters['_locale'] = $request->server->get('TULIA_WEBSITE_LOCALE');
        $parameters['_content_locale'] = $request->server->get('TULIA_WEBSITE_LOCALE');
        $parameters['_website_id'] = $request->server->get('TULIA_WEBSITE_ID');

        $request->attributes->add($parameters);

        $request->setLocale($parameters['_locale']);
        $request->setDefaultLocale($request->server->get('TULIA_WEBSITE_LOCALE_DEFAULT'));
    }

    public function setDefaultLocale(RequestEvent $event)
    {
        $request = $event->getRequest();
        $request->setDefaultLocale($request->server->get('TULIA_WEBSITE_LOCALE_DEFAULT'));
        $this->router?->getContext()->setParameter('_locale', $request->server->get('TULIA_WEBSITE_LOCALE_DEFAULT'));
        $this->router?->getContext()->setParameter('_website_id', $request->server->get('TULIA_WEBSITE_ID'));
    }
}
