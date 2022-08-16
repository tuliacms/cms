<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Internal\Importer;

use Tulia\Cms\Content\Attributes\Infrastructure\Importer\ObjectDataToAttributesTransformer;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Node\Application\UseCase\CreateNode;
use Tulia\Cms\Node\Application\UseCase\CreateNodeRequest;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeRepositoryInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Component\Importer\ObjectImporter\ObjectImporterInterface;
use Tulia\Component\Importer\Structure\ObjectData;

/**
 * @author Adam Banaszkiewicz
 */
class NodeEntryImporter implements ObjectImporterInterface
{
    public function __construct(
        private readonly NodeRepositoryInterface $repository,
        private readonly CreateNode $createNode,
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
        private readonly AuthenticatedUserProviderInterface $authenticatedUserProvider,
    ) {
    }

    public function import(ObjectData $objectData): ?array
    {
        $details = [
            'title' => $objectData['title'],
            'slug' => $objectData['slug'] ?? $objectData['title'] ?? null,
            'status' => $objectData['status'] ?? 'published',
            'purposes' => $objectData['purposes'] ?? [],
            'published_at' => $objectData['published_at'] ?? ImmutableDateTime::now(),
            'published_to' => $objectData['published_to'] ?? null,
        ];
        ($this->createNode)(new CreateNodeRequest(
            $objectData['type'],
            $this->authenticatedUserProvider->getUser()->getId(),
            $details,
            $this->transformObjectDataToAttributes($objectData),
            'en_US'
        ));

        return null;
    }

    private function transformObjectDataToAttributes(ObjectData $objectData): array
    {
        $transformer = new ObjectDataToAttributesTransformer(
            $this->contentTypeRegistry->get($objectData['type'])
        );

        return $transformer->transform($objectData->toArray()['attributes']);
    }
}
