<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Taxonomy;

use Tulia\Cms\Node\Domain\WriteModel\Service\ParentTermsResolverInterface;
use Tulia\Cms\Taxonomy\UserInterface\OpenHost\Query\ParentTermsQueryService;

/**
 * @author Adam Banaszkiewicz
 */
final class TaxonomyParentTermsResolver implements ParentTermsResolverInterface
{
    public function __construct(
        private readonly ParentTermsQueryService $parentTermsQueryService,
    ) {
    }

    public function fetchAllParents(string $term, string $taxonomy): array
    {
       return $this->parentTermsQueryService->fetchAllParents($term, $taxonomy);
    }
}
