<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UninstallThemeRequest implements RequestInterface
{
    public function __construct(
        public readonly string $theme,
    ) {
    }
}
