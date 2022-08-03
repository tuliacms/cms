<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Domain\ReadModel\Service;

use Tulia\Cms\Content\Attributes\Domain\ReadModel\Model\AttributeValue;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\ValueRendering\AttributesValueRenderer;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Service\UriToArrayTransformer;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractAttributesFinder implements AttributesFinderInterface
{
    public function __construct(
        private UriToArrayTransformer $uriToArrayTransformer,
        private AttributesValueRenderer $attributesValueRenderer,
    ) {
    }

    abstract protected function query(string $ownerId, string $locale): array;

    public function find(string $ownerId, string $locale): array
    {
        $attributes = $this->attributesValueRenderer->renderValues($this->query($ownerId, $locale));
        $result = $this->uriToArrayTransformer->transform($attributes);

        foreach ($result as $key => $value) {
            $result[$key] = new AttributeValue($value);
        }

        return $result;
    }
}
