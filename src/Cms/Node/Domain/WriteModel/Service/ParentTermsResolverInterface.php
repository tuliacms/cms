<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface ParentTermsResolverInterface
{
    public function fetchAllParents(string $term, string $taxonomy): array;
}
