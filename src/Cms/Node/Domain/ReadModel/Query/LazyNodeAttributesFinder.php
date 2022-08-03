<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ReadModel\Query;

use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\AttributesFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class LazyNodeAttributesFinder
{
    public function __construct(
        private string $nodeId,
        private string $locale,
        private AttributesFinderInterface $attributesFinder
    ) {
    }

    public function find(): array
    {
        return $this->attributesFinder->find($this->nodeId, $this->locale);
    }
}
