<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Theme\Domain\WriteModel\Service\DefaultThemeConfigurationProviderInterface;
use Tulia\Cms\Theme\Domain\WriteModel\Service\IdGeneratorInterface;
use Tulia\Cms\Theme\Domain\WriteModel\ThemeCustomizationRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class NewCustomization extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly ThemeCustomizationRepositoryInterface $repository,
        private readonly IdGeneratorInterface $idGenerator,
        private readonly DefaultThemeConfigurationProviderInterface $provider,
    ) {
    }

    /**
     * @param RequestInterface&NewCustomizationRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $customization = $this->repository->get($request->theme, $request->websiteId);

        $id = $customization->modify(
            $this->idGenerator,
            $this->provider,
            $request->availableLocales,
        );

        $this->repository->save($customization);

        return new IdResult($id);
    }
}
