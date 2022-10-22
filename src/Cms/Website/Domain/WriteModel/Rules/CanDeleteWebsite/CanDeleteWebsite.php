<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Rules\CanDeleteWebsite;

use Tulia\Cms\Website\Domain\WriteModel\Query\CurrentWebsiteProviderInterface;
use Tulia\Cms\Website\Domain\WriteModel\Query\WebsitesCounterQueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CanDeleteWebsite implements CanDeleteWebsiteInterface
{
    public function __construct(
        private readonly WebsitesCounterQueryInterface $websitesCounterQuery,
        private readonly CurrentWebsiteProviderInterface $currentWebsiteProvider,
    ) {
    }

    public function decide(string $id): CanDeleteWebsiteReasonEnum
    {
        if ($this->thereAreNoActiveWebsites($id)) {
            return CanDeleteWebsiteReasonEnum::AtLeastOneWebsiteMustBeActive;
        }
        if ($this->deletedWebsiteIsACurrentOne($id)) {
            return CanDeleteWebsiteReasonEnum::CannotDeleteCurrentWebsite;
        }

        return CanDeleteWebsiteReasonEnum::OK;
    }

    private function thereAreNoActiveWebsites(string $id): bool
    {
        return 0 === $this->websitesCounterQuery->countActiveOnesExcept($id);
    }

    private function deletedWebsiteIsACurrentOne(string $id): bool
    {
        return $this->currentWebsiteProvider->getId() === $id;
    }
}
