<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\UseCase;

use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\SslModeEnum;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotAddLocaleException;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanAddLocale\CanAddLocale;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanAddLocale\CanAddLocaleReasonEnum;
use Tulia\Cms\Website\Domain\WriteModel\Service\TranslationCopyMachine\TranslationsCopyMachineFactory;
use Tulia\Cms\Website\Domain\WriteModel\WebsiteRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class AddLocale extends AbstractTransactionalUseCase
{
    private const TRANSLATIONS_THRESHOLD = 400;

    public function __construct(
        private readonly WebsiteRepositoryInterface $repository,
        private readonly EventBusInterface $eventBus,
        private readonly TranslationsCopyMachineFactory $copyMachineFactory,
    ) {
    }

    /**
     * @param RequestInterface&AddLocaleRequest $request
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
            throw CannotAddLocaleException::fromReason(CanAddLocaleReasonEnum::TooManyTranslations, $request->code, $request->websiteId);
        }

        $website->addLocale(
            new CanAddLocale(),
            $request->code,
            $request->domain,
            $request->domainDevelopment,
            $request->localePrefix,
            $request->pathPrefix,
            SslModeEnum::tryFrom($request->sslMode),
        );

        $this->repository->save($website);

        if ($request->copyMachineMode !== CopyMachineEnum::DISABLE_PROCESSING) {
            $copiedTranslations = $copyMachine->copyTo($request->code);

            if ($translationsToCopy > $copiedTranslations) {
                throw new \Exception(
                    sprintf('Copied translations %d of %d.', $copiedTranslations, $translationsToCopy)
                );
            }
        }

        $this->eventBus->dispatchCollection($website->collectDomainEvents());

        return null;
    }
}
