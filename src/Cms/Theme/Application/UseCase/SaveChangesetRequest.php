<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class SaveChangesetRequest implements RequestInterface
{
    public function __construct(
        public readonly string $theme,
        public readonly string $websiteId,
        public readonly string $locale,
        public readonly string $changesetId,
        public readonly array $payload,
        public readonly bool $activate,
    ) {
    }
}
