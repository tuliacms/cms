<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Rules\CanDeleteNode;

use Tulia\Cms\Node\Domain\WriteModel\Service\NodeChildrenDetectorInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CanDeleteNode implements CanDeleteNodeInterface
{
    public function __construct(
        private NodeChildrenDetectorInterface $nodeChildrenDetector
    ) {
    }

    public function decide(string $nodeId): CanDeleteNodeReasonEnum
    {
        if ($this->nodeHasChildren($nodeId)) {
            return CanDeleteNodeReasonEnum::NodeHasChildren;
        }

        return CanDeleteNodeReasonEnum::OK;
    }

    private function nodeHasChildren(string $nodeId): bool
    {
        return $this->nodeChildrenDetector->hasChildren($nodeId);
    }
}
