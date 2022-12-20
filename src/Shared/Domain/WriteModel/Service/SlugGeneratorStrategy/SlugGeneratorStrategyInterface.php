<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy;

/**
 * @author Adam Banaszkiewicz
 */
interface SlugGeneratorStrategyInterface
{
    public function generate(
        ?string $slug,
        ?string $nameOrTitle,
    ): string;

    public function generateGloballyUnique(
        string $elementId,
        ?string $slug,
        ?string $nameOrTitle,
        string $websiteId,
        string $locale,
    ): string;
}
