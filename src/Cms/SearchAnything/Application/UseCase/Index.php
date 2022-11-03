<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Application\UseCase;

use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteRegistryInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Enum\LocalizationStrategyEnum;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Enum\MultisiteStrategyEnum;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexRegistry;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class Index extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly WebsiteRegistryInterface $websiteRegistry,
        private readonly TranslatorInterface $translator,
        private readonly IndexRegistry $indexRegistry,
    ) {
    }

    /**
     * @param RequestInterface&IndexRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $defaultLocale = $this->translator->getLocale();
        $website = $this->websiteRegistry->get($request->websiteId);

        foreach ($this->indexRegistry->all() as $index) {
            if ($index->isMultisiteStrategy(MultisiteStrategyEnum::GLOBAL)) {
                $this->indexPartially($index, $defaultLocale);
            } else {
                if ($index->isLocalizationStrategy(LocalizationStrategyEnum::UNILINGUAL)) {
                    $this->indexPartially($index, $defaultLocale, $website->getId());
                } else {
                    foreach ($website->getLocales() as $locale) {
                        $this->indexPartially($index, $locale->getCode(), $website->getId(), $locale->getCode());
                    }
                }
            }
        }

        return null;
    }

    private function indexPartially(
        IndexInterface $index,
        string $translationLocale,
        ?string $websiteId = null,
        ?string $locale = null,
    ): void {
        if ($this->translator instanceof LocaleAwareInterface) {
            $this->translator->setLocale($translationLocale);
        }

        $index->clear($websiteId, $locale);
        $offset = 0;
        $limit = 100;

        do {
            $delta = $index->getDelta();
            $index->getCollector()->collect($index, $websiteId, $locale, $offset, $limit);
            $offset += $limit;
        } while ($delta !== $index->getDelta());
    }
}
