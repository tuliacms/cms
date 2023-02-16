<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder;

/**
 * @author Adam Banaszkiewicz
 */
final class Criteria
{
    private function __construct(
        public readonly string $by,
        public readonly string $value,
        public readonly string $websiteId,
        public readonly string $locale,
    ) {
    }

    public static function bySpace(string $space, string $websiteId, string $locale): self
    {
        return new self('space', $space, $websiteId, $locale);
    }
}
