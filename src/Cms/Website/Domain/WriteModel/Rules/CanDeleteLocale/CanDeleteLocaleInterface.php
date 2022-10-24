<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Rules\CanDeleteLocale;

/**
 * @author Adam Banaszkiewicz
 */
interface CanDeleteLocaleInterface
{
    public function decide(
        string $code,
        string $websiteId,
        array $codes,
        string $defaultLocale,
    ): CanDeleteLocaleReasonEnum;
}
