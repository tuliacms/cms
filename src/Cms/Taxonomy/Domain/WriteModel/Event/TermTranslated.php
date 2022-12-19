<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
final class TermTranslated extends AbstractDomainEvent
{
    public function __construct(
        public readonly string $termId,
        public readonly string $taxonomyType,
        public readonly string $websiteId,
        public readonly string $locale,
    ) {
    }
}
