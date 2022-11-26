<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Application\Service;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Adam Banaszkiewicz
 */
final class FrameworkCacheService
{
    public function __construct(
        private readonly string $kernelCacheFilepath,
        private readonly EventDispatcherInterface $dispatcher,
    ) {
    }

    public function clear(): void
    {
        if (php_sapi_name() === 'cli') {
            (new Filesystem())->remove($this->kernelCacheFilepath);
        } else {
            $this->dispatcher->addListener(KernelEvents::TERMINATE, function () {
                (new Filesystem())->remove($this->kernelCacheFilepath);
            });
        }
    }
}
