<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\OpenHost\Query;

/**
 * @author Adam Banaszkiewicz
 */
final class ParentTermsQueryService
{
    public function __construct()
    {
    }

    public function fetchAllParents(string $term, string $taxonomy): array
    {
        return [];
    }
}
