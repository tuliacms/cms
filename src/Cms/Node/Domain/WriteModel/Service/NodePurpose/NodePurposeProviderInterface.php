<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Service\NodePurpose;

/**
 * @author Adam Banaszkiewicz
 */
interface NodePurposeProviderInterface
{
    public function provide(): array;
}
