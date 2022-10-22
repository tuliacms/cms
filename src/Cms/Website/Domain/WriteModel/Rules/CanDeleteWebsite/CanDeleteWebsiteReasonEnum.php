<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Rules\CanDeleteWebsite;

/**
 * @author Adam Banaszkiewicz
 */
enum CanDeleteWebsiteReasonEnum: string
{
    case AtLeastOneWebsiteMustBeActive = 'At least one website must be active';
    case CannotDeleteCurrentWebsite = 'Cannot delete current website';
    case OK = 'OK';
}
