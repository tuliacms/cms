<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotDeleteLocaleException;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanDeleteLocale\CanDeleteLocaleInterface;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanDeleteLocale\CanDeleteLocaleReasonEnum;
use Tulia\Cms\Website\Domain\WriteModel\Service\TranslationCopyMachine\TranslationsCopyMachineFactory;
use Tulia\Cms\Website\Domain\WriteModel\WebsiteRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DeleteLocale extends AbstractTransactionalUseCase
{
    private const TRANSLATIONS_THRESHOLD = 0;

    public function __construct(
        private readonly WebsiteRepositoryInterface $repository,
        private readonly EventBusInterface $eventBus,
        private readonly CanDeleteLocaleInterface $canDeleteLocale,
        private readonly TranslationsCopyMachineFactory $copyMachineFactory,
    ) {
    }

    /**
     * @param RequestInterface&DeleteLocaleRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $website = $this->repository->get($request->websiteId);

        $defaultLocaleCode = $website->getDefaultLocaleCode();
        $copyMachine = $this->copyMachineFactory->create($request->websiteId, $defaultLocaleCode);
        $translationsToCopy = $copyMachine->count();

        if (
            $request->copyMachineMode === CopyMachineEnum::RESPECT_THRESHOLD
            && $translationsToCopy > self::TRANSLATIONS_THRESHOLD
        ) {
            throw CannotDeleteLocaleException::fromReason(CanDeleteLocaleReasonEnum::TooManyTranslations, $request->code, $request->websiteId);
        }

        $website->deleteLocale($this->canDeleteLocale, $request->code);

        $this->repository->save($website);

        if ($request->copyMachineMode !== CopyMachineEnum::DISABLE_PROCESSING) {
            $deletedTranslations = $copyMachine->deleteFrom($request->code);

            if ($translationsToCopy > $deletedTranslations) {
                throw new \Exception(
                    sprintf('Deleted translations %d of %d.', $deletedTranslations, $translationsToCopy)
                );
            }
        }

        $this->eventBus->dispatchCollection($website->collectDomainEvents());

        return null;
    }
}
