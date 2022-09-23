<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Theme\Domain\WriteModel\Service\DefaultThemeConfigurationProviderInterface;
use Tulia\Cms\Theme\Domain\WriteModel\ThemeCustomizationRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class SaveChangeset extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly ThemeCustomizationRepositoryInterface $repository,
        private readonly DefaultThemeConfigurationProviderInterface $configurationProvider,
    ) {
    }

    /**
     * @param RequestInterface&SaveChangesetRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $customization = $this->repository->get($request->theme, $request->websiteId);
        $customization->updateChangeset(
            $request->changesetId,
            $request->locale,
            $this->configurationProvider,
            $request->payload
        );

        if ($request->activate) {
            $customization->activateChangeset($request->changesetId);
        }

        $this->repository->save($customization);

        return null;
    }
}
