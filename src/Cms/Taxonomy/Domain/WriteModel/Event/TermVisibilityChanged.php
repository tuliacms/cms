<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
final class TermVisibilityChanged extends AbstractDomainEvent
{
    public function __construct(
        public readonly string $termId,
        public readonly string $taxonomyType,
        public readonly string $websiteId,
        public readonly array $visibility,
    ) {
    }

    public function isVisibleIn(string $locale): bool
    {
        return $this->visibility[$locale] ?? throw new \InvalidArgumentException(sprintf('There is no translation for %s locale, cannot fetch visibility.', $locale));
    }
}
