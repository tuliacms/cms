<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Service\SlugGeneratorStrategy;

/**
 * @author Adam Banaszkiewicz
 */
interface SlugGeneratorStrategyInterface
{
    public function generate(
        string $locale,
        string $slug,
        string $title,
        string $nodeId
    ): string;
}
