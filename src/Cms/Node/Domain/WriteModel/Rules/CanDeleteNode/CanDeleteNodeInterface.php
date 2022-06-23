<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Rules\CanDeleteNode;

/**
 * @author Adam Banaszkiewicz
 */
interface CanDeleteNodeInterface
{
    public function decide(string $nodeId): CanDeleteNodeReasonEnum;
}
