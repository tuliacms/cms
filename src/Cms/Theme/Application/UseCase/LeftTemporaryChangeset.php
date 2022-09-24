<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Theme\Domain\WriteModel\ThemeCustomizationRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class LeftTemporaryChangeset extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly ThemeCustomizationRepositoryInterface $repository,
    ) {
    }

    /**
     * @param RequestInterface&LeftTemporaryChangesetRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $customization = $this->repository->get($request->theme, $request->websiteId);
        $customization->deleteTemporaryChangeset($request->changesetId);

        $this->repository->save($customization);

        return null;
    }
}
