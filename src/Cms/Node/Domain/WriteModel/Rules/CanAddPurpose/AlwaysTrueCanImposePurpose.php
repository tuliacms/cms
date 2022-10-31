<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose;

use Tulia\Cms\Node\Domain\WriteModel\Model\Purpose;

/**
 * @author Adam Banaszkiewicz
 */
final class AlwaysTrueCanImposePurpose implements CanImposePurposeInterface
{
    public function decide(string $nodeId, string $websiteId, Purpose $purpose, Purpose ...$purposes): CanImposePurposeReasonEnum
    {
        return CanImposePurposeReasonEnum::OK;
    }
}
