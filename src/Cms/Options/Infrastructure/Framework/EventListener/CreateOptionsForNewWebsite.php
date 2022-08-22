<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Infrastructure\Framework\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Options\Application\Service\WebsitesOptionsRegistrator;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteCreated;

/**
 * @author Adam Banaszkiewicz
 */
final class CreateOptionsForNewWebsite implements EventSubscriberInterface
{
    public function __construct(
        private readonly WebsitesOptionsRegistrator $websitesOptionsRegistrator
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WebsiteCreated::class => ['handle', 0],
        ];
    }

    public function handle(WebsiteCreated $event): void
    {
        $this->websitesOptionsRegistrator->registerMissingOptions($event->id);
    }
}
