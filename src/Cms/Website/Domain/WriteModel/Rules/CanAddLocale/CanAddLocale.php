<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Rules\CanAddLocale;

/**
 * @author Adam Banaszkiewicz
 */
final class CanAddLocale implements CanAddLocaleInterface
{
    public function decide(
        string $code,
        array $locales,
    ): CanAddLocaleReasonEnum {
        if ($this->localeAlreadyExists($code, $locales)) {
            return CanAddLocaleReasonEnum::LocaleAlreadyExists;
        }

        return CanAddLocaleReasonEnum::OK;
    }

    private function localeAlreadyExists(string $code, array $locales): bool
    {
        return \in_array($code, $locales, true);
    }
}
