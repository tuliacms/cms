<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\ReadModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface TermsOfTaxonomiesQueryInterface
{
    public function allTermsGrouppedByTaxonomy(string $websiteId, string $locale): array;
}
