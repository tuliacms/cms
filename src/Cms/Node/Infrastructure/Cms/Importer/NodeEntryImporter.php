<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Importer;

use Tulia\Cms\Content\Attributes\Infrastructure\Importer\ObjectDataToAttributesTransformer;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Node\Application\UseCase\CreateNode;
use Tulia\Cms\Node\Application\UseCase\CreateNodeRequest;
use Tulia\Cms\Node\Domain\WriteModel\Exception\CannotImposePurposeToNodeException;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Component\Importer\Exception\ObjectImportFailedContextAwareException;
use Tulia\Component\Importer\ObjectImporter\ObjectImporterInterface;
use Tulia\Component\Importer\ObjectImporter\Traits\WebsiteAwareTrait;
use Tulia\Component\Importer\Structure\ObjectData;

/**
 * @author Adam Banaszkiewicz
 */
class NodeEntryImporter implements ObjectImporterInterface
{
    use WebsiteAwareTrait;

    public function __construct(
        private readonly CreateNode $createNode,
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
        private readonly AuthenticatedUserProviderInterface $userProvider,
    ) {
    }

    public function import(ObjectData $objectData): ?string
    {
        $details = [
            'title' => $objectData['title'],
            'slug' => $objectData['slug'] ?? $objectData['title'] ?? null,
            'status' => $objectData['status'] ?? 'published',
            'purposes' => $objectData['purposes'] ?? [],
            'published_at' => $objectData['published_at'] ?? ImmutableDateTime::now(),
            'published_to' => $objectData['published_to'] ?? null,
            'main_category' => $objectData['main_category'] ?? null,
            'additional_categories' => $objectData['additional_categories'] ?? [],
        ];
        try {
            /** @var IdResult $result */
            $result = ($this->createNode)(
                new CreateNodeRequest(
                    $objectData['type'],
                    $this->userProvider->getUser()->getId(),
                    $details + ['attributes' => $this->transformObjectDataToAttributes($objectData)],
                    $this->getWebsite()->getId(),
                    $this->getWebsite()->getLocale()->getCode(),
                    $this->getWebsite()->getDefaultLocale()->getCode(),
                    $this->getWebsite()->getLocaleCodes(),
                )
            );
        } catch (CannotImposePurposeToNodeException $e) {
            throw ObjectImportFailedContextAwareException::fromContext(
                sprintf(
                    'Imported object "%s" has singular purpose "%s" imposed to another node in this website. Plase make sure You don\'t have any nodes with purpose "%s" in website before importing.',
                    $objectData['title'],
                    $e->purpose,
                    $e->purpose,
                )
            );
        }

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
