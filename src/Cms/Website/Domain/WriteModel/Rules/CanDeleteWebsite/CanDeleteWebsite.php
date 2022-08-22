<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Rules\CanDeleteWebsite;

use Tulia\Cms\Website\Domain\WriteModel\Query\WebsitesCounterQueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CanDeleteWebsite implements CanDeleteWebsiteInterface
{
    public function __construct(
        private readonly WebsitesCounterQueryInterface $websitesCounterQuery
    ) {
    }

    public function decide(string $id): CanDeleteWebsiteReasonEnum
    {
        if ($this->thereAreNoActiveWebsites($id)) {
            return CanDeleteWebsiteReasonEnum::AtLeastOneWebsiteMustBeActive;
        }

        return CanDeleteWebsiteReasonEnum::OK;
    }

    private function thereAreNoActiveWebsites(string $id): bool
    {
        return 0 === $this->websitesCounterQuery->countActiveOnesExcept($id);
    }
}
