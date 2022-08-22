<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Rules\CanTurnOffWebsite;

/**
 * @author Adam Banaszkiewicz
 */
interface CanTurnOffWebsiteInterface
{
    public function decide(
        string $id,
    ): CanTurnOffWebsiteReasonEnum;
}
