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

    public function makeOtherNodeHasSpecificPurpose(string $purpose): void
    {
        $this->counts[$purpose] = 1;
    }

    public function makeOtherNodeHasntSpecificPurpose(string $purpose): void
    {
        unset($this->counts[$purpose]);
    }

    public function countOtherNodesWithPurpose(string $localNode, string $websiteId, string $purpose): int
    {
        return $this->counts[$purpose] ?? 0;
    }
}
