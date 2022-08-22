<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Rules\CanDeleteWebsite;

/**
 * @author Adam Banaszkiewicz
 */
interface CanDeleteWebsiteInterface
{
    public function decide(string $id): CanDeleteWebsiteReasonEnum;
}
