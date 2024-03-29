<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Customizer;

use Requtize\Assetter\AssetterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Tulia\Component\Theme\Customizer\DetectorInterface;

/**
 * Loads default frontend customizer assets when customizer
 * mode is enabled in Request.
 *
 * @author Adam Banaszkiewicz
 */
class AssetsLoader implements EventSubscriberInterface
{
    public function __construct(
        private readonly AssetterInterface $assetter,
        private readonly DetectorInterface $detector
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['handle', 500],
        ];
    }

    public function handle(RequestEvent $event): void
    {
        if ($this->detector->isCustomizerMode()) {
            $this->assetter->require('customizer.front');
        }
    }
}
