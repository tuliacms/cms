<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Theme;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Tulia\Component\Templating\EventListener\ResponseViewRenderer;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
final class ViewOverwritingResponseViewRenderer implements EventSubscriberInterface
{
    public function __construct(
        private readonly ResponseViewRenderer $renderer,
        private readonly ThemeViewOverwriteProducer $overwriteProducer,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ViewEvent::class => ['onKernelView', 500],
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        if (! $event->getControllerResult() instanceof View) {
            return;
        }

        $view = $event->getControllerResult();
        $view->setViews($this->overwriteProducer->produce('cms', $view->getViews()));

        $this->renderer->onKernelView($event);
    }
}
