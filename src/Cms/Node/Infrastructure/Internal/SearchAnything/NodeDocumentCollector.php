<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Internal\SearchAnything;

use Tulia\Cms\Node\Domain\ReadModel\Query\NodeSearchCollectorInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\AbstractDocumentCollector;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class NodeDocumentCollector extends AbstractDocumentCollector
{
    public function __construct(
        private NodeSearchCollectorInterface $collector
    ) {
    }

    public function collect(IndexInterface $index, string $locale, int $offset, int $limit): void
    {
        foreach ($this->collector->collectDocumentsOfLocale($locale, $offset, $limit) as $node) {
            $document = $index->document($node['id']);
            $document->setLink('backend.node.edit', ['id' => $node['id'], 'node_type' => $node['type']]);
            $document->setTitle($node['title']);

            $index->save($document);
        }
    }

    public function countDocuments(string $locale): int
    {
        return $this->collector->countDocumentsOfLocale($locale);
    }

    public function getIndex(): string
    {
        return 'node';
    }
}
