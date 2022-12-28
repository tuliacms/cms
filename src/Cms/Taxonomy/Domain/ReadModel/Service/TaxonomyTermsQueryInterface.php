<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\ReadModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface TaxonomyTermsQueryInterface
{
    public function collectVisibleTerms(string $websiteId, string $locale): array;
}
