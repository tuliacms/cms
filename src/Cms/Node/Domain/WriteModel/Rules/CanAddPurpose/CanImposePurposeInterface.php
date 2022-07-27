<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose;

use Tulia\Cms\Node\Domain\WriteModel\Model\Purpose;

/**
 * @author Adam Banaszkiewicz
 */
interface CanImposePurposeInterface
{
    public function decide(
        string $nodeId,
        Purpose $purpose,
        Purpose ...$purposes
    ): CanImposePurposeReasonEnum;
}
