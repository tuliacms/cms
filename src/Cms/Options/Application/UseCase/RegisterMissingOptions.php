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
        foreach ($this->fetchWebsiteIdList($request->websiteIdList) as $id) {
            $this->optionsRegistrator->registerMissingOptions($id);
        }

        return null;
    }

    /**
     * @return string[]
     */
    private function fetchWebsiteIdList(array $websites): array
    {
        $ids = array_map(
            static fn($v) => $v->getId(),
            $this->finder->all()
        );

        if ($websites !== []) {
            $ids = array_intersect($ids, $websites);
        }

        return $ids;
    }
}
