<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy;

/**
 * @author Adam Banaszkiewicz
 */
interface SlugExistenceCheckerInterface
{
    public function exists(string $slug, string $elementId, string $websiteId, string $locale): bool;
}
