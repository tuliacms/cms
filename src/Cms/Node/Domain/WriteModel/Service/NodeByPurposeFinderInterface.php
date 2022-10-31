<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeByPurposeFinderInterface
{
    public function countOtherNodesWithPurpose(string $localNode, string $websiteId, string $purpose): int;
}
