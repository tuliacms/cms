<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Rules\CanTurnOffWebsite;

/**
 * @author Adam Banaszkiewicz
 */
enum CanTurnOffWebsiteReasonEnum: string
{
    case AtLeastOneWebsiteMustBeActive = 'At least one website must be active';
    case OK = 'OK';
}
