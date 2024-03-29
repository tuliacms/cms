<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Framework\Bus\EventBus\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\ContentTypeCreated;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\ContentTypeDeleted;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\ContentTypeUpdated;
use Tulia\Cms\Content\Type\Infrastructure\Persistence\ContentProvider\CachedContentTypeRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class CacheClearerSubscriber implements EventSubscriberInterface
{
    private CachedContentTypeRegistry $registry;

    public function __construct(CachedContentTypeRegistry $registry)
    {
        $this->registry = $registry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContentTypeCreated::class => 'clearCache',
            ContentTypeUpdated::class => 'clearCache',
            ContentTypeDeleted::class => 'clearCache',
        ];
    }

    public function clearCache(): void
    {
        $this->registry->clearCache();
    }
}
