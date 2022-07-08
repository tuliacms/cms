<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Adam Banaszkiewicz
 */
class RequestMatcher implements EventSubscriberInterface
{
    protected RouterInterface $router;
    protected RouterListener $symfonyListener;

    public function __construct(
        RouterInterface $router,
        RouterListener $symfonyListener
    ) {
        $this->router = $router;
        $this->symfonyListener = $symfonyListener;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 200],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->attributes->has('_controller')) {
            return;
        }

        $pathinfo = urldecode($request->getPathInfo());
        $fakeRequest = $this->createRequest($pathinfo, $request);
        $fakeEvent = new RequestEvent($event->getKernel(), $fakeRequest, $event->getRequestType());

        $this->symfonyListener->onKernelRequest($fakeEvent);

        $request->attributes->replace($fakeRequest->attributes->all());
    }

    private function createRequest(string $pathinfo, Request $originalRequest): Request
    {
        $uri = $pathinfo;
        $serverData = [];

        $host = $originalRequest->getHost() ?: 'localhost';

        if ('https' === $originalRequest->getScheme() && 443 !== $originalRequest->getPort()) {
            $host .= ':' . $originalRequest->getPort();
        } elseif ('http' === $originalRequest->getScheme() && 80 !== $originalRequest->getPort()) {
            $host .= ':' . $originalRequest->getPort();
        }

        $uri = $originalRequest->getScheme() . '://' . $host . $uri . '?' . $originalRequest->getQueryString();

        if ($originalRequest->getMethod() === 'GET') {
            $parameters = $originalRequest->query->all();
        } else {
            $parameters = $originalRequest->request->all();
        }

        $request = Request::create($uri, $originalRequest->getMethod(), $parameters, [], [], $serverData);
        $request->attributes->add($originalRequest->attributes->all());
        $request->setDefaultLocale($originalRequest->getDefaultLocale());
        $request->setLocale($originalRequest->getLocale());

        return $request;
    }
}
