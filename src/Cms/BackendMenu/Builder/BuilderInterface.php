<?php

declare(strict_types=1);

namespace Tulia\Cms\BackendMenu\Builder;

use Tulia\Cms\BackendMenu\Builder\Registry\ItemRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface BuilderInterface
{
    public function build(ItemRegistryInterface $registry, string $websiteId, string $locale): void;
}
