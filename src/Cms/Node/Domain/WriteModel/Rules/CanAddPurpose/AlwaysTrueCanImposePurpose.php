<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose;

/**
 * @author Adam Banaszkiewicz
 */
final class AlwaysTrueCanImposePurpose implements CanImposePurposeInterface
{
    public function decide(string $nodeId, string $purpose, string $websiteId, array $purposes): CanImposePurposeReasonEnum
    {
        return CanImposePurposeReasonEnum::OK;
    }
}
