<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Application\Service;

use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Adam Banaszkiewicz
 */
final class FrameworkCacheService
{
    public function __construct(
        private readonly string $kernelCacheFilepath
    ) {
    }

    public function clear(): void
    {
        (new Filesystem())->remove($this->kernelCacheFilepath);
    }
}
