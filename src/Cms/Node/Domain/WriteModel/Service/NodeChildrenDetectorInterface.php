<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeChildrenDetectorInterface
{
    public function hasChildren(string $nodeId): bool;
}
