<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class EnableLocaleRequest implements RequestInterface
{
    public function __construct(
        public readonly string $websiteId,
        public readonly string $code,
    ) {
    }
}
