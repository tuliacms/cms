<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateWebsiteRequest implements RequestInterface
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly bool $active,
        public readonly array $locales,
    ) {
    }
}
