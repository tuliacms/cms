<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateTermRequest implements RequestInterface
{
    public function __construct(
        public readonly string $taxonomyType,
        public readonly string $termId,
        public readonly array $details,
        public readonly array $attributes,
        public readonly string $websiteId,
        public readonly string $locale,
        public readonly string $defaultLocale,
        public readonly array $locales,
    ) {
    }
}
