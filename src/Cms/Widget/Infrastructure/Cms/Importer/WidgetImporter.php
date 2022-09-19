<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Cms\Importer;

use Tulia\Cms\Content\Attributes\Infrastructure\Importer\ObjectDataToAttributesTransformer;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\Widget\Application\UseCase\CreateWidget;
use Tulia\Cms\Widget\Application\UseCase\CreateWidgetRequest;
use Tulia\Component\Importer\ObjectImporter\ObjectImporterInterface;
use Tulia\Component\Importer\ObjectImporter\Traits\WebsiteAwareTrait;
use Tulia\Component\Importer\Structure\ObjectData;

/**
 * @author Adam Banaszkiewicz
 */
final class WidgetImporter implements ObjectImporterInterface
{
    use WebsiteAwareTrait;

    public function __construct(
        private readonly CreateWidget $createWidget,
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
    ) {
    }

    public function import(ObjectData $objectData): ?string
    {
        $details = [
            'name' => $objectData['name'],
            'space' => $objectData['space'],
            'visibility' => $objectData['visibility'] ?? true,
            'title' => $objectData['title'] ?? '',
            'styles' => $objectData['styles'] ?? [],
            'htmlClass' => $objectData['html_class'] ?? null,
            'htmlId' => $objectData['html_id'] ?? null,
        ];
        /** @var IdResult $result */
        $result = ($this->createWidget)(new CreateWidgetRequest(
            $objectData['type'],
            $details,
            $this->transformObjectDataToAttributes($objectData),
            $this->getWebsite()->getId(),
            $this->getWebsite()->getLocale()->getCode(),
            $this->getWebsite()->getDefaultLocale()->getCode(),
            $this->getWebsite()->getLocaleCodes(),
        ));

        return $result->id;
    }

    private function transformObjectDataToAttributes(ObjectData $objectData): array
    {
        $transformer = new ObjectDataToAttributesTransformer(
            $this->contentTypeRegistry->get(str_replace('.', '_', 'widget_'.$objectData['type']))
        );

        return $transformer->transform($objectData->toArray()['attributes']);
    }
}
