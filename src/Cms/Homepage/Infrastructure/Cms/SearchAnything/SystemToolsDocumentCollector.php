<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\Infrastructure\Cms\SearchAnything;

use Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles\DashboardTilesRegistryInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\DocumentCollectorInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class SystemToolsDocumentCollector implements DocumentCollectorInterface
{
    public function __construct(
        private DashboardTilesRegistryInterface $tilesRegistry,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function collect(IndexInterface $index, string $locale, int $offset, int $limit): void
    {
        // No pagination in this collector. Next pages should be empty.
        if ($offset !== 0) {
            return;
        }

        $tiles = array_merge(
            $this->tilesRegistry->getTiles('tools'),
            $this->tilesRegistry->getTiles('system')
        );

        foreach ($tiles as $i => $tile) {
            $document = $index->document(sprintf('tile_%d', $i));
            $document->setTitle($tile['name']);
            $document->setLink($tile['route']);

            $index->save($document);
        }
    }

    public function countDocuments(string $locale): int
    {
        return count(array_merge(
            $this->tilesRegistry->getTiles('tools'),
            $this->tilesRegistry->getTiles('system')
        ));
    }

    public function getIndex(): string
    {
        return 'tools';
    }
}
