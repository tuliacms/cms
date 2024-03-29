<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Framework\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Node\Domain\ReadModel\Query\NodeSearchCollectorInterface;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeCreated;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeDeleted;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeUpdated;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexRegistry;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateSearchAnythingWhenNodeUpdates implements EventSubscriberInterface
{
    public function __construct(
        private readonly IndexRegistry $indexRegistry,
        private readonly NodeSearchCollectorInterface $collector,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NodeUpdated::class => 'index',
            NodeCreated::class => 'index',
            NodeDeleted::class => 'delete',
        ];
    }

    public function index(NodeUpdated|NodeCreated $event): void
    {
        foreach ($this->collector->collectTranslationsOfNode($event->id) as $node) {
            $index = $this->indexRegistry->get('node');
            $document = $index->document($node['id'], $node['website_id'], $node['locale']);
            $document->setTitle($node['title']);
            $document->setLink('backend.node.edit', ['node_type' => $node['type'], 'id' => $node['id']]);

            $index->save($document);
        }
    }

    public function delete(NodeDeleted $event): void
    {
        foreach ($event->translatedTo as $locale) {
            $index = $this->indexRegistry->get('node');

            $document = $index->document($event->id, $event->websiteId, $locale);

            $index->delete($document);
        }
    }
}
