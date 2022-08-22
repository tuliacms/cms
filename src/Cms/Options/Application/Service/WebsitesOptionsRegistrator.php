<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Application\Service;

use Tulia\Cms\Options\Domain\WriteModel\OptionsRepositoryInterface;
use Tulia\Cms\Options\Domain\WriteModel\Service\MissingOptionsService;

/**
 * @author Adam Banaszkiewicz
 */
class WebsitesOptionsRegistrator
{
    public function __construct(
        private readonly OptionsRepositoryInterface $repository,
        private readonly MissingOptionsService $missingOptionsService,
    ) {
    }

    /**
     * Creates all registered options for given website ID.
     * Website must exists to create options for this website.
     */
    public function registerMissingOptions(string $websiteId): void
    {
        $options = $this->missingOptionsService->collectMissingOptionsForWebsite($websiteId);

        foreach ($options as $option) {
            $this->repository->save($option);
        }
    }

    public function removeOptions(string $websiteId): void
    {
        $options = $this->repository->getAllForWebsite($websiteId);

        foreach ($options as $option) {
            $this->repository->delete($option);
        }
    }
}
