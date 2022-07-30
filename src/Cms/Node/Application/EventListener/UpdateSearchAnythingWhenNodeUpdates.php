<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tulia\Cms\Node\Domain\ReadModel\Query\NodeSearchCollectorInterface;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeCreated;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeDeleted;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeUpdated;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexerInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateSearchAnythingWhenNodeUpdates implements EventSubscriberInterface
{
    public function __construct(
        private IndexerInterface $indexer,
        private NodeSearchCollectorInterface $collector,
        private UrlGeneratorInterface $generator
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
            $index = $this->indexer->index('node', $node['locale']);
            $document = $index->document($node['id']);
            $document->setTitle($node['title']);
            $document->setLink('backend.node.edit', ['node_type' => $node['type'], 'id' => $node['id']]);

            $index->save($document);
        }
    }

    public function delete(NodeDeleted $event): void
    {
        foreach ($event->translatedTo as $locale) {
            $index = $this->indexer->index('node', $locale);

            $document = $index->document($event->id);

            $index->delete($document);
        }
    }
}
