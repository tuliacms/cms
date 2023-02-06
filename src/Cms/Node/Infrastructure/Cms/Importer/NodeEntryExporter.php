<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Importer;

use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderScopeEnum;
use Tulia\Cms\Node\Domain\ReadModel\Query\NodeExportCollectorInterface;
use Tulia\Cms\Node\Infrastructure\Persistence\Doctrine\Dbal\ReadModel\DbalNodeAttributesFinder;
use Tulia\Component\Importer\ObjectExporter\ObjectExporterInterface;
use Tulia\Component\Importer\ObjectExporter\ObjectsCollection\ObjectsCollection;
use Tulia\Component\Importer\ObjectExporter\ObjectsCollection\ObjectToExport;
use Tulia\Component\Importer\ObjectExporter\Traits\AttributeObjectBuilderTrait;
use Tulia\Component\Importer\ObjectExporter\Traits\WebsiteAwareTrait;
use Tulia\Component\Importer\Structure\ObjectData;
use Tulia\Component\Importer\Structure\ObjectDataFactory;

/**
 * @author Adam Banaszkiewicz
 */
class NodeEntryExporter implements ObjectExporterInterface
{
    use WebsiteAwareTrait;
    use AttributeObjectBuilderTrait;

    public function __construct(
        private readonly NodeFinderInterface $nodeFinder,
        private readonly DbalNodeAttributesFinder $attributesFinder,
        private readonly ObjectDataFactory $objectDataFactory,
        private readonly NodeExportCollectorInterface $exportCollector,
    ) {
    }

    public function collectObjects(ObjectsCollection $collection): void
    {
        foreach ($this->exportCollector->collect($this->getWebsite()->getId(), $this->getWebsite()->getLocale()->getCode()) as $node) {
            $collection->addObject(new ObjectToExport('Node', $node['id'], $node['title']));
        }
    }

    public function export(ObjectData $objectData): void
    {
        $node = $this->nodeFinder->findOne([
            'id' => $objectData->getObjectId(),
            'website_id' => $this->getWebsite()->getId(),
            'locale' => $this->getWebsite()->getLocale()->getCode(),
        ], NodeFinderScopeEnum::SINGLE);

        $objectData['type'] = $node->getType();
        $objectData['title'] = $node->getTitle();
        $objectData['slug'] = $node->getSlug();

        if ($node->getParentId()) {
            $objectData['parent_id'] = $node->getParentId();
        }
        if ($node->getMainCategory()) {
            $objectData['main_category'] = $node->getMainCategory();
        }
        if ($node->getAdditionalCategories()) {
            $objectData['additional_categories'] = $node->getAdditionalCategories();
        }
        if ($node->getPurposes()) {
            $objectData['purposes'] = $node->getPurposes();
        }
        $objectData['attributes'] = $this->buildAttributes(
            $this->attributesFinder,
            $node->getId(),
            $node->getLocale()
        );
    }
}
