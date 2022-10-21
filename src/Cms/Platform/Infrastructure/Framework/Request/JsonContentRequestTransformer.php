<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Request;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * @author Adam Banaszkiewicz
 */
final class JsonContentRequestTransformer implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'transformRequestContent',
        ];
    }

    public function transformRequestContent(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (false === $this->isDecodeable($request)) {
            return;
        }

        $contentType = $request->headers->get('Content-Type');

        $format = null === $contentType
            ? $request->getRequestFormat()
            : $request->getFormat($contentType);

        $content = match($format) {
            'json' => (array) json_decode((string) $request->getContent(), true, 512, JSON_THROW_ON_ERROR),
            default => $request->request->all(),
        };

        $request->request->replace($content);
    }

    private function isDecodeable(Request $request): bool
    {
        return in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'], true);
    }
}
