<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class ResetThemeCustomizationRequest implements RequestInterface
{
    public function __construct(
        public readonly string $theme,
        public readonly string $websiteId,
    ) {
    }
}
