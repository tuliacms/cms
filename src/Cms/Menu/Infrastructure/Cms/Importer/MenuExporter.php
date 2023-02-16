<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Cms\Importer;

use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderScopeEnum;
use Tulia\Cms\Menu\Domain\ReadModel\Query\MenuExportCollectorInterface;
use Tulia\Component\Importer\ObjectExporter\ObjectExporterInterface;
use Tulia\Component\Importer\ObjectExporter\ObjectsCollection\ObjectsCollection;
use Tulia\Component\Importer\ObjectExporter\ObjectsCollection\ObjectToExport;
use Tulia\Component\Importer\ObjectExporter\Traits\WebsiteAwareTrait;
use Tulia\Component\Importer\Structure\ObjectData;
use Tulia\Component\Importer\Structure\ObjectDataFactory;

/**
 * @author Adam Banaszkiewicz
 */
class MenuExporter implements ObjectExporterInterface
{
    use WebsiteAwareTrait;

    public function __construct(
        private readonly MenuFinderInterface $menuFinder,
        private readonly ObjectDataFactory $objectDataFactory,
        private readonly MenuExportCollectorInterface $exportCollector,
    ) {
    }

    public function collectObjects(ObjectsCollection $collection): void
    {
        foreach ($this->exportCollector->collect($this->getWebsite()->getId()) as $menu) {
            $collection->addObject(new ObjectToExport('Menu', $menu['id'], $menu['name']));
        }
    }

    public function export(ObjectData $objectData): void
    {
        $menu = $this->menuFinder->findOne([
            'id' => $objectData->getObjectId(),
            'website_id' => $this->getWebsite()->getId(),
            'locale' => $this->getWebsite()->getLocale()->getCode(),
            'fetch_root' => true,
        ], MenuFinderScopeEnum::BACKEND_SINGLE);

        $objectData['name'] = $menu->getName();
        $objectData['spaces'] = $menu->getSpaces();
        $objectData['items'] = $this->collectItems($menu->getItems(), $menu->getRootItemId());
    }

    private function collectItems(array $items, ?string $parent = null): array
    {
        $result = [];

        foreach ($items as $item) {
            if ($item->getParentId() === $parent) {
                $result[] = $this->objectDataFactory->create('MenuItem', [
                    '@id' => $item->getId(),
                    'link_type' => $item->getType(),
                    'link_identity' => $item->getIdentity(),
                    'name' => $item->getName(),
                    'hash' => $item->getHash(),
                    'position' => $item->getPosition(),
                    'children' => $item->getParentId() ? $this->collectItems($items, $item->getId()) : null,
                ]);
            }
        }

        return $result;
    }
}
