<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Node\TestDoubles;

use Tulia\Cms\Node\Domain\WriteModel\Service\NodeByPurposeFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class NodeByPurposeFinderStub implements NodeByPurposeFinderInterface
{
    private array $counts = [];

    public function makeOtherNodeHasSpecificPurpose(string $purpose, string $websiteId): void
    {
        $this->counts[$websiteId][$purpose] = 1;
    }

    public function makeOtherNodeHasntSpecificPurpose(string $purpose, string $websiteId): void
    {
        unset($this->counts[$websiteId][$purpose]);
    }

    public function countOtherNodesWithPurpose(string $localNode, string $purpose, string $websiteId): int
    {
        return $this->counts[$websiteId][$purpose] ?? 0;
    }
}
