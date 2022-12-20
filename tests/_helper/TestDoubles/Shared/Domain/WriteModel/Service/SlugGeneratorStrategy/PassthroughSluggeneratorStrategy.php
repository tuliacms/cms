<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Helper\TestDoubles\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy;

use Tulia\Cms\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugGeneratorStrategyInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class PassthroughSluggeneratorStrategy implements SlugGeneratorStrategyInterface
{
    public function generate(?string $slug, ?string $nameOrTitle): string
    {
        return $slug ?? $nameOrTitle ?? throw new \Exception('Empty $slug and $title.');
    }

    public function generateGloballyUnique(
        string $elementId,
        ?string $slug,
        ?string $nameOrTitle,
        string $websiteId,
        string $locale,
    ): string {
        return $slug ?? $nameOrTitle ?? throw new \Exception('Empty $slug and $title.');
    }
}
