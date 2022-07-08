<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * @author Adam Banaszkiewicz
 */
class LocaleResolver implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['handle', 9500],
        ];
    }

    public function handle(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $parameters = $request->attributes->all();
        $parameters['_locale'] = $request->server->get('TULIA_WEBSITE_LOCALE');
        $parameters['_content_locale'] = $request->server->get('TULIA_WEBSITE_LOCALE');

        $request->attributes->add($parameters);

        $request->setLocale($parameters['_locale'] ?? 'en_US');
        $request->setDefaultLocale($request->server->get('TULIA_WEBSITE_LOCALE_DEFAULT'));
    }
}
