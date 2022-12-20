<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Domain\ReadModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
final class LazyAttributesFinder
{
    public function __construct(
        private readonly string $nodeId,
        private readonly string $locale,
        private readonly AttributesFinderInterface $attributesFinder,
    ) {
    }

    public function find(): array
    {
        return $this->attributesFinder->find($this->nodeId, $this->locale);
    }
}
