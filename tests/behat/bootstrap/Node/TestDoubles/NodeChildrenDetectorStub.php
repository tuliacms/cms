<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Node\TestDoubles;

use Tulia\Cms\Node\Domain\WriteModel\Service\NodeChildrenDetectorInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class NodeChildrenDetectorStub implements NodeChildrenDetectorInterface
{
    private array $nodesWithChildren = [];

    public function makeNodeHasChildren(string $nodeId, string $child): void
    {
        $this->nodesWithChildren[$nodeId] = $child;
    }

    public function hasChildren(string $nodeId): bool
    {
        return isset($this->nodesWithChildren[$nodeId]);
    }
}
