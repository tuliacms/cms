<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Framework\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Website\Application\Service\WebsiteDynamicConfiguration;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteCreated;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteDeleted;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteUpdated;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateWebsitesDynamicConfigWhenAnyWebsiteChanges implements EventSubscriberInterface
{
    public function __construct(
        private readonly WebsiteDynamicConfiguration $configuration
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WebsiteUpdated::class => 'update',
            WebsiteCreated::class => 'update',
            WebsiteDeleted::class => 'update',
        ];
    }

    public function update(): void
    {
        $this->configuration->update();
    }
}
