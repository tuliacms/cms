<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Application\UseCase;

use Tulia\Cms\Options\Application\Service\WebsitesOptionsRegistrator;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Website\Domain\ReadModel\Finder\WebsiteFinderInterface;
use Tulia\Cms\Website\Domain\ReadModel\Finder\WebsiteFinderScopeEnum;
use Tulia\Cms\Website\Domain\ReadModel\Model\Website;

/**
 * @author Adam Banaszkiewicz
 */
final class RegisterMissingOptions extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly WebsitesOptionsRegistrator $optionsRegistrator,
        private readonly WebsiteFinderInterface $finder,
    ) {
    }

    /**
     * @param RequestInterface&RegisterMissingOptionsRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        foreach ($this->fetchWebsites($request->websiteIdList) as $website) {
            $this->optionsRegistrator->registerMissingOptions($website->getId());
        }

        return null;
    }

    /**
     * @return Website[]
     */
    private function fetchWebsites(array $websites): Collection
    {
        $criteria = [];

        if ($websites !== []) {
            $criteria = ['id__in' => $websites];
        }

        return $this->finder->find($criteria, WebsiteFinderScopeEnum::INTERNAL);
    }
}
