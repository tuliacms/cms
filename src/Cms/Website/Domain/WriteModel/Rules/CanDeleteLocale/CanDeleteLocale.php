<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Rules\CanDeleteLocale;

use Tulia\Cms\Website\Domain\WriteModel\Query\CurrentWebsiteProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CanDeleteLocale implements CanDeleteLocaleInterface
{
    public function __construct(
        private readonly CurrentWebsiteProviderInterface $currentWebsiteProvider,
    ) {
    }

    public function decide(
        string $code,
        string $websiteId,
        array $codes,
        string $defaultLocale,
    ): CanDeleteLocaleReasonEnum {
        if ($this->localeDoesNotExists($code, $codes)) {
            return CanDeleteLocaleReasonEnum::LocaleDoesNotExists;
        }
        if ($this->isCurrentLocale($code, $websiteId)) {
            return CanDeleteLocaleReasonEnum::CannotDeleteLocaleThatYouAreOn;
        }
        if ($this->isDefaultLocale($code, $defaultLocale)) {
            return CanDeleteLocaleReasonEnum::CannotDeleteDefaultLocale;
        }

        return CanDeleteLocaleReasonEnum::OK;
    }

    private function localeDoesNotExists(string $code, array $codes): bool
    {
        return !\in_array($code, $codes, true);
    }

    private function isCurrentLocale(string $code, string $websiteId): bool
    {
        return $this->currentWebsiteProvider->getLocale() === $code
            && $this->currentWebsiteProvider->getId() === $websiteId;
    }

    private function isDefaultLocale(string $code, string $defaultLocale): bool
    {
        return $code === $defaultLocale;
    }
}
