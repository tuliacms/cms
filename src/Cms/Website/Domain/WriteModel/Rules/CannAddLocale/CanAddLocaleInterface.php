<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Rules\CannAddLocale;

/**
 * @author Adam Banaszkiewicz
 */
interface CanAddLocaleInterface
{
    public function decide(
        string $code,
        array $locales,
    ): CanAddLocaleReasonEnum;
}
