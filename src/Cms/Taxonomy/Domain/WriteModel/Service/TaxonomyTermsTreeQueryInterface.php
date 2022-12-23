<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface TaxonomyTermsTreeQueryInterface
{
    public function getTermsTreeFor(string $taxonomy, string $websiteId, string $locale): array;
}
