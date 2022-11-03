<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\Infrastructure\Cms\SearchAnything;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles\DashboardTilesRegistryInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\AbstractDocumentCollector;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class SystemToolsDocumentCollector extends AbstractDocumentCollector
{
    public function __construct(
        private readonly DashboardTilesRegistryInterface $tilesRegistry,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function collect(IndexInterface $index, ?string $websiteId, ?string $locale, int $offset, int $limit): void
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
            $document = $index->document(sprintf('tile_%d', $i), $websiteId, $locale);
            $document->setTitle($tile['name']);
            $document->setLink($tile['route']);

            $index->save($document);
        }

        $this->addSpecialPages($index, $websiteId, $locale);
    }

    public function countDocuments(string $websiteId, string $locale): int
    {
        return count(array_merge(
            $this->tilesRegistry->getTiles('tools'),
            $this->tilesRegistry->getTiles('system')
        ));
    }

    private function addSpecialPages(IndexInterface $index, string $websiteId, string $locale): void
    {
        $document = $index->document('system_page_dashboard', $websiteId, $locale);
        $document->setTitle($this->translator->trans('dashboard'));
        $document->setLink('backend.homepage');

        $index->save($document);

        $document = $index->document('system_page_tools', $websiteId, $locale);
        $document->setTitle($this->translator->trans('tools'));
        $document->setLink('backend.tools');

        $index->save($document);

        $document = $index->document('system_page_system', $websiteId, $locale);
        $document->setTitle($this->translator->trans('system'));
        $document->setLink('backend.system');

        $index->save($document);

        $document = $index->document('system_page_settings', $websiteId, $locale);
        $document->setTitle($this->translator->trans('settings'));
        $document->setLink('backend.settings');

        $index->save($document);
    }
}
