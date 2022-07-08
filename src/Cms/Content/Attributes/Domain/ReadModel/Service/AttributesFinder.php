<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Domain\ReadModel\Service;

use Tulia\Cms\Content\Attributes\Domain\ReadModel\Model\AttributeValue;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\ValueRendering\AttributesValueRenderer;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Service\UriToArrayTransformer;

/**
 * @author Adam Banaszkiewicz
 */
class AttributesFinder
{
    private AttributeReadStorageInterface $finder;
    private UriToArrayTransformer $uriToArrayTransformer;
    private AttributesValueRenderer $attributesValueRenderer;

    public function __construct(
        AttributeReadStorageInterface $finder,
        UriToArrayTransformer $uriToArrayTransformer,
        AttributesValueRenderer $attributesValueRenderer
    ) {
        $this->finder = $finder;
        $this->uriToArrayTransformer = $uriToArrayTransformer;
        $this->attributesValueRenderer = $attributesValueRenderer;
    }

    public function findAllAggregated(string $type, string $scope, array $ownerIdList, string $locale): array
    {
        $source = $this->finder->findAll($type, $ownerIdList, $locale);
        $result = [];

        foreach ($source as $ownerId => $attributes) {
            $attributes = $this->attributesValueRenderer->renderValues($attributes, $type, $scope);
            $result[$ownerId] = $this->uriToArrayTransformer->transform($attributes);

            foreach ($result[$ownerId] as $key => $value) {
                $result[$ownerId][$key] = new AttributeValue($value);
            }
        }

        return $result;
    }

    public function findAll(string $type, string $scope, string $ownerId, string $locale): array
    {
        return $this->findAllAggregated($type, $scope, [$ownerId], $locale)[$ownerId] ?? [];
    }
}
