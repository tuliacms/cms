<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateTermsHierarchyRequest implements RequestInterface
{
    public function __construct(
        public readonly string $taxonomyType,
        public readonly string $websiteId,
        public readonly array $hierarchy,
    ) {
    }
}
