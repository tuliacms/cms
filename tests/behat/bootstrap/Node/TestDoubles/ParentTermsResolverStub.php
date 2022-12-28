<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Node\TestDoubles;

use Tulia\Cms\Node\Domain\WriteModel\Service\ParentTermsResolverInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class ParentTermsResolverStub implements ParentTermsResolverInterface
{
    private array $terms = [];

    public function fetchAllParents(string $term, string $taxonomy, string $websiteId): array
    {
        return $this->terms[$taxonomy][$term] ?? [];
    }

    public function store(string $term, string $taxonomy, string $parents): void
    {
        $this->terms[$taxonomy][$term] = explode(',', $parents);
    }
}
