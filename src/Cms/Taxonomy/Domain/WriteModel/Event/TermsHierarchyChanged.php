<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
final class TermsHierarchyChanged extends AbstractDomainEvent
{
    public function __construct(
        public readonly string $taxonomyType,
        public readonly string $websiteId,
        public readonly array $termsHierarchy,
    ) {
    }

    public function isChildOf(string $term, string $parent): bool
    {
        return isset($this->termsHierarchy[$parent]) && in_array($term, $this->termsHierarchy[$parent], true);
    }

    public function isRoot(string $term): bool
    {
        foreach ($this->termsHierarchy as $children) {
            if (in_array($term, $children, true)) {
                return false;
            }
        }

        return true;
    }
}
