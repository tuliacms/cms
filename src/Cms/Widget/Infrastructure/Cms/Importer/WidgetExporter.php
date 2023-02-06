<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Cms\Importer;

use Tulia\Cms\Widget\Domain\ReadModel\Finder\WidgetFinderInterface;
use Tulia\Cms\Widget\Domain\ReadModel\Finder\WidgetFinderScopeEnum;
use Tulia\Cms\Widget\Domain\ReadModel\Model\Widget;
use Tulia\Cms\Widget\Infrastructure\Persistence\Doctrine\Dbal\DbalWidgetAttributesFinder;
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
final class WidgetExporter implements ObjectExporterInterface
{
    use WebsiteAwareTrait;
    use AttributeObjectBuilderTrait;

    public function __construct(
        private readonly WidgetFinderInterface $widgetFinder,
        private readonly DbalWidgetAttributesFinder $attributesFinder,
        private readonly ObjectDataFactory $objectDataFactory,
    ) {
    }

    public function collectObjects(ObjectsCollection $collection): void
    {
        $widgets = $this->widgetFinder->find([
            'website_id' => $this->getWebsite()->getId(),
            'locale' => $this->getWebsite()->getLocale()->getCode(),
        ], WidgetFinderScopeEnum::BACKEND_LISTING);

        /** @var Widget $widget */
        foreach ($widgets->toArray() as $widget) {
            $collection->addObject(new ObjectToExport('Widget', $widget->getId(), $widget->getName()));
        }
    }

    public function export(ObjectData $objectData): void
    {
        $widget = $this->widgetFinder->findOne([
            'website_id' => $this->getWebsite()->getId(),
            'locale' => $this->getWebsite()->getLocale()->getCode(),
            'id' => $objectData->getObjectId(),
        ], WidgetFinderScopeEnum::BACKEND_LISTING);

        $objectData['title'] = $widget->getTitle();
        $objectData['type'] = $widget->getWidgetType();
        $objectData['space'] = $widget->getSpace();
        $objectData['name'] = $widget->getName();
        $objectData['html_class'] = $widget->getHtmlClass();
        $objectData['html_id'] = $widget->getHtmlId();
        $objectData['styles'] = $widget->getStyles();
        $objectData['attributes'] = $this->buildAttributes(
            $this->attributesFinder,
            $widget->getId(),
            $widget->getLocale()
        );
    }
}
