<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Rules\CanTurnOffWebsite;

use Tulia\Cms\Website\Domain\WriteModel\Query\WebsitesCounterQueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CanTurnOffWebsite implements CanTurnOffWebsiteInterface
{
    public function __construct(
        private readonly WebsitesCounterQueryInterface $websitesCounterQuery
    ) {
    }

    public function decide(
        string $id,
    ): CanTurnOffWebsiteReasonEnum {
        if ($this->thereIsNoOtherActiveWebsite($id)) {
            return CanTurnOffWebsiteReasonEnum::AtLeastOneWebsiteMustBeActive;
        }

        return CanTurnOffWebsiteReasonEnum::OK;
    }

    private function thereIsNoOtherActiveWebsite(string $id): bool
    {
        return 0 === $this->websitesCounterQuery->countActiveOnesExcept($id);
    }
}
