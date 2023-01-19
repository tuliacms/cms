<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Cms\Import;

use Tulia\Cms\Content\Attributes\Infrastructure\Importer\ObjectDataToAttributesTransformer;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\Taxonomy\Application\UseCase\CreateTerm;
use Tulia\Cms\Taxonomy\Application\UseCase\CreateTermRequest;
use Tulia\Component\Importer\ObjectImporter\ObjectImporterInterface;
use Tulia\Component\Importer\ObjectImporter\Traits\WebsiteAwareTrait;
use Tulia\Component\Importer\Structure\ObjectData;

/**
 * @author Adam Banaszkiewicz
 */
final class TermImporter implements ObjectImporterInterface
{
    use WebsiteAwareTrait;

    public function __construct(
        private readonly CreateTerm $createTerm,
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
    ) {
    }

    public function import(ObjectData $objectData): ?string
    {
        $website = $this->getWebsite();
        $data = [
            'name' => $objectData['name'],
            'slug' => $objectData['slug'],
            'attributes' => $this->transformObjectDataToAttributes($objectData),
            'parent' => $objectData['parent_id'],
        ];

        /** @var IdResult $result */
        $result = ($this->createTerm)(new CreateTermRequest(
            $objectData['type'],
            $data,
            $website->getId(),
            $website->getLocale()->getCode(),
            $website->getDefaultLocale()->getCode(),
            $website->getLocaleCodes(),
        ));

        return $result->id;
    }

    private function transformObjectDataToAttributes(ObjectData $objectData): array
    {
        $transformer = new ObjectDataToAttributesTransformer(
            $this->contentTypeRegistry->get($objectData['type'])
        );

        return $transformer->transform($objectData->toArray()['attributes']);
    }
}
