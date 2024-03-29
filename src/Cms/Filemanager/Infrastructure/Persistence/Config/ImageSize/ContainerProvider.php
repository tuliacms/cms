<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Infrastructure\Persistence\Config\ImageSize;

use Tulia\Cms\Filemanager\Domain\ImageSize\ImagesSizeProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContainerProvider implements ImagesSizeProviderInterface
{
    public function __construct(
        private readonly array $imageSizes,
    ) {
    }

    public function provide(): array
    {
        return $this->imageSizes;
    }
}
