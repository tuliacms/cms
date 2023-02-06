<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Cms\Import;

use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderScopeEnum;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Model\Term;
use Tulia\Cms\Taxonomy\Infrastructure\Persistence\Doctrine\Dbal\DbalTermAttributesFinder;
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
final class TermExporter implements ObjectExporterInterface
{
    use WebsiteAwareTrait;
    use AttributeObjectBuilderTrait;

    public function __construct(
        private readonly TermFinderInterface $termFinder,
        private readonly DbalTermAttributesFinder $attributesFinder,
        private readonly ObjectDataFactory $objectDataFactory,
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
    ) {
    }

    public function collectObjects(ObjectsCollection $collection): void
    {
        foreach ($this->contentTypeRegistry->allByType('taxonomy') as $contentType) {
            $terms = $this->termFinder->find([
                'website_id' => $this->getWebsite()->getId(),
                'locale' => $this->getWebsite()->getLocale()->getCode(),
                'taxonomy_type' => $contentType->getCode(),
                'sort_hierarchical' => true,
            ], TermFinderScopeEnum::INTERNAL);

            /** @var Term $term */
            foreach ($terms->toArray() as $term) {
                $collection->addObject(new ObjectToExport('Term', $term->getId(), '['.$contentType->getName().'] '.str_repeat(' - ', $term->getLevel() - 1).$term->getName()));
            }
        }
    }

    public function export(ObjectData $objectData): void
    {
        $term = $this->termFinder->findOne([
            'website_id' => $this->getWebsite()->getId(),
            'locale' => $this->getWebsite()->getLocale()->getCode(),
            'id' => $objectData->getObjectId(),
        ], TermFinderScopeEnum::INTERNAL);

        $objectData['type'] = $term->getType();
        $objectData['name'] = $term->getName();
        $objectData['slug'] = $term->getSlug();
        $objectData['parent_id'] = $term->getParentId();
        $objectData['position'] = $term->getPosition();
        $objectData['attributes'] = $this->buildAttributes(
            $this->attributesFinder,
            $term->getId(),
            $term->getLocale()
        );
    }
}
