<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Taxonomy;

use Tulia\Cms\Node\Domain\WriteModel\Service\ParentTermsResolverInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\ParentTermsQueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class TaxonomyParentTermsResolver implements ParentTermsResolverInterface
{
    public function __construct(
        private readonly ParentTermsQueryInterface $parentTermsQuery,
    ) {
    }

    public function fetchAllParents(string $term, string $taxonomy, string $websiteId): array
    {
       return $this->parentTermsQuery->fetchAllParents($term, $taxonomy, $websiteId);
    }
}
