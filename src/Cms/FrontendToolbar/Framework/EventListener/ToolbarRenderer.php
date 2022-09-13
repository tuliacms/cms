<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Framework\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Tulia\Cms\FrontendToolbar\Builder\Builder;

/**
 * @author Adam Banaszkiewicz
 */
class ToolbarRenderer implements EventSubscriberInterface
{
    public function __construct(
        private readonly Builder $builder,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => '__invoke',
        ];
    }

    public function __invoke(ResponseEvent $event): void
    {
        $request = $event->getRequest();

        if (
            null === $this->tokenStorage->getToken()
            || $this->authorizationChecker->isGranted('ROLE_ADMIN') === false
            || $request->server->get('TULIA_WEBSITE_IS_BACKEND')
            || strncmp($request->getPathInfo(), '/_wdt', 5) === 0
            || strncmp($request->getPathInfo(), '/_profiler', 10) === 0
        ) {
            return;
        }

        $stylepath = $request->getUriForPath('/assets/core/frontend-toolbar/frontend-toolbar.css');

        $response = $event->getResponse();
        $content = $response->getContent();

        $toolbar = $this->builder->build($request);
        $toolbar .= '<link rel="stylesheet" type="text/css" href="' . $stylepath . '" />';

        $content = str_replace('</body>', $toolbar . '</body>', $content);

        $response->setContent($content);
    }
}
