<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Framework\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Node\Domain\ReadModel\Persistence\CategoriesPersistenceInterface;
use Tulia\Cms\Node\Domain\WriteModel\Event\AttributeUpdated;

/**
 * @author Adam Banaszkiewicz
 */
class AttributeUpdatedListener implements EventSubscriberInterface
{
    private ContentTypeRegistryInterface $contentTypeRegistry;
    private CategoriesPersistenceInterface $categoriesPersistence;

    public function __construct(
        ContentTypeRegistryInterface $contentTypeRegistry,
        CategoriesPersistenceInterface $categoriesPersistence
    ) {
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->categoriesPersistence = $categoriesPersistence;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AttributeUpdated::class => ['processUpdate', 0],
        ];
    }

    public function processUpdate(AttributeUpdated $event): void
    {
        // @todo Rewrite persising categories in Repository implementation
        return;
        $type = $this->contentTypeRegistry->get($event->getNodeType());

        if ($type->hasField($event->getAttribute())) {
            $field = $type->getField($event->getAttribute());

            if ($field->getType() === 'taxonomy') {
                $this->categoriesPersistence->update(
                    $event->getNodeId(),
                    $field->getConfig('taxonomy'),
                    $event->getValue() ? [$event->getValue() => 'MAIN'] : []
                );
            }
        }
    }
}
