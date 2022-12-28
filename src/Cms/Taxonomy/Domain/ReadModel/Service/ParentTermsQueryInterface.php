<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\ReadModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface ParentTermsQueryInterface
{
    public function fetchAllParents(string $term, string $taxonomy, string $websiteId): array;
}
