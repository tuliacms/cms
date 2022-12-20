<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Service\SlugGeneratorStrategy;

/**
 * @author Adam Banaszkiewicz
 */
interface SlugGeneratorStrategyInterface
{
    public function generate(
        string $nodeId,
        string $slug,
        string $title,
        string $websiteId,
        ?string $locale = null,
    ): string;
}
